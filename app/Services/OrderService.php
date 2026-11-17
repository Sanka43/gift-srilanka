<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Create order atomically: order, items, stock reduction, payment status.
     * Uses row-level lock on products to prevent negative stock and race conditions.
     */
    public function createOrder(array $cartItems, array $checkoutData): Order
    {
        if (empty($cartItems)) {
            throw ValidationException::withMessages(['cart' => ['Your cart is empty.']]);
        }

        $productIds = array_unique(array_column($cartItems, 'product_id'));
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $missing = array_diff($productIds, $products->keys()->all());
        if (! empty($missing)) {
            throw ValidationException::withMessages(['cart' => ['Some items are no longer available. Please update your cart.']]);
        }

        foreach ($cartItems as $item) {
            $product = $products->get($item['product_id']);
            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty < 1) {
                throw ValidationException::withMessages(['cart' => ['Invalid quantity for an item.']]);
            }
            if ($product->stock < $qty) {
                throw ValidationException::withMessages(['cart' => ["Not enough stock for \"{$product->name}\". Available: {$product->stock}."]]);
            }
        }

        $deliveryCharge = (float) config('shop.delivery_charge', 350);
        $paymentStatus = $this->paymentService->getInitialPaymentStatus($checkoutData['payment_method']);

        return DB::transaction(function () use ($cartItems, $checkoutData, $products, $deliveryCharge, $paymentStatus) {
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $product = $products->get($item['product_id']);
                $qty = (int) ($item['quantity'] ?? 0);
                $price = (float) ($product->discount_price ?? $product->price);
                $subtotal += $price * $qty;
            }
            $subtotal = round($subtotal, 2);
            $total = round($subtotal + $deliveryCharge, 2);

            $order = Order::create([
                'user_id' => auth()->id(),
                'full_name' => $checkoutData['full_name'],
                'phone' => $checkoutData['phone'],
                'address' => $checkoutData['address'],
                'district' => $checkoutData['district'],
                'delivery_date' => $checkoutData['delivery_date'] ?? null,
                'delivery_time' => $checkoutData['delivery_time'] ?? null,
                'special_note' => $checkoutData['special_note'] ?? null,
                'total_amount' => $total,
                'delivery_charge' => $deliveryCharge,
                'status' => Order::STATUS_PENDING,
                'payment_method' => $checkoutData['payment_method'],
                'payment_status' => $paymentStatus,
            ]);

            foreach ($cartItems as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (! $product) {
                    throw ValidationException::withMessages(['cart' => ['A product in your cart is no longer available. Please update your cart.']]);
                }
                if ($product->stock < (int) $item['quantity']) {
                    throw ValidationException::withMessages(['cart' => ["Not enough stock for \"{$product->name}\". Available: {$product->stock}. Please update your cart."]]);
                }
                $price = (float) ($product->discount_price ?? $product->price);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => (int) $item['quantity'],
                    'price' => $price,
                ]);
                $product->decrement('stock', (int) $item['quantity']);
            }

            return $order;
        });
    }

    public function cancelOrder(Order $order, ?string $note = null): void
    {
        if ($order->status === Order::STATUS_CANCELLED) {
            return;
        }

        DB::transaction(function () use ($order, $note) {
            $order->loadMissing('items');

            foreach ($order->items as $item) {
                if ($item->product_id && $item->quantity > 0) {
                    Product::where('id', $item->product_id)->increment('stock', (int) $item->quantity);
                }
            }

            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancel_requested' => false,
            ]);

            $order->statusHistories()->create([
                'status' => Order::STATUS_CANCELLED,
                'note' => $note ?? 'Order cancelled and stock restored.',
            ]);
        });
    }

    public function calculateTotal(array|Collection $cartItems): float
    {
        if ($cartItems instanceof Collection) {
            $cartItems = $cartItems->all();
        }
        $cartItems = array_filter($cartItems, fn ($item) => ! empty($item['product_id']) && ! empty($item['quantity']));
        if (empty($cartItems)) {
            return 0.0;
        }

        $productIds = array_unique(array_column($cartItems, 'product_id'));
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $total = 0;
        foreach ($cartItems as $item) {
            $product = $products->get($item['product_id'] ?? null);
            if ($product) {
                $price = (float) ($product->discount_price ?? $product->price);
                $total += $price * (int) ($item['quantity'] ?? 0);
            }
        }
        return round($total, 2);
    }
}
