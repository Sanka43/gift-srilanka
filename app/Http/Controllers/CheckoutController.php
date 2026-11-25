<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\DeliveryFeeService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orderService,
        private DeliveryFeeService $delivery
    ) {}

    public function index(): View|RedirectResponse
    {
        $items = $this->cart->itemsWithProducts();
        if ($items->isEmpty()) {
            return redirect()->route('shop.index')->with('info', 'Your cart is empty.');
        }
        session()->put('checkout_token', \Illuminate\Support\Str::random(64));
        $subtotal = $this->cart->total();
        $deliveryCharge = $this->delivery->defaultCharge();
        $total = round($subtotal + $deliveryCharge, 2);
        $deliverySettings = $this->delivery->settings();
        return view('checkout.index', compact('items', 'subtotal', 'deliveryCharge', 'total', 'deliverySettings'));
    }

    public function quote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $subtotal = (float) $this->cart->total();
        $quote = $this->delivery->quote((float) $data['lat'], (float) $data['lng'], $subtotal);

        return response()->json($quote + [
            'subtotal' => $subtotal,
            'total' => round($subtotal + $quote['charge'], 2),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $items = $this->cart->itemsWithProducts();
        if ($items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }
        $validated = $request->validated();
        $order = $this->orderService->createOrder(
            $this->cart->getCart()->all(),
            $validated
        );
        $this->cart->clear();
        session()->forget('checkout_token');

        // If paying online via Card / Gateway, redirect to payment gateway
        if (($validated['payment_method'] ?? '') === 'online') {
            return redirect()->route('checkout.payment.pay', $order)
                ->setStatusCode(303);
        }

        try {
            Mail::to($order->customer_email)->queue(new OrderConfirmationMail($order));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Order confirmation email queue failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            try {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            } catch (\Throwable $sendException) {
                \Illuminate\Support\Facades\Log::error('Order confirmation email send failed', [
                    'order_id' => $order->id,
                    'message' => $sendException->getMessage(),
                ]);
            }
        }

        return redirect()->route('checkout.confirmation', $order)->with('success', 'Order placed successfully.')
            ->setStatusCode(303);
    }

    public function confirmation(Order $order): View|RedirectResponse
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403, 'You do not have access to this order.');
        }
        $order->load('items.product');
        return view('checkout.confirmation', compact('order'));
    }
}
