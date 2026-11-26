<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function getCart(): Collection
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())
                ->get()
                ->map(fn (Cart $cart) => [
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                ]);
        }

        return collect(session(self::SESSION_KEY, []));
    }

    /**
     * Check if we can add this quantity for the product (stock check).
     * Returns ['ok' => bool, 'message' => string].
     */
    public function canAddQuantity(int $productId, int $quantity): array
    {
        $product = Product::find($productId);
        if (! $product) {
            return ['ok' => false, 'message' => 'Product not found.'];
        }
        $existingItem = $this->getCart()->firstWhere('product_id', $productId);
        $existingQty = $existingItem ? (int) ($existingItem['quantity'] ?? 0) : 0;
        $newTotal = $existingQty + $quantity;
        if ($product->stock < $newTotal) {
            return ['ok' => false, 'message' => "Not enough stock. In cart: {$existingQty}, max total: {$product->stock}."];
        }
        return ['ok' => true, 'message' => ''];
    }

    /**
     * Check if we can set this quantity for the product (stock check).
     * Returns ['ok' => bool, 'message' => string].
     */
    public function canSetQuantity(int $productId, int $quantity): array
    {
        $product = Product::find($productId);
        if (! $product) {
            return ['ok' => false, 'message' => 'Product not found.'];
        }
        if ($product->stock < $quantity) {
            return ['ok' => false, 'message' => "Not enough stock for {$product->name}. Available: {$product->stock}."];
        }
        return ['ok' => true, 'message' => ''];
    }

    public function add(int $productId, int $quantity = 1): void
    {
        if (auth()->check()) {
            $cart = Cart::firstOrNew([
                'user_id' => auth()->id(),
                'product_id' => $productId,
            ]);
            $cart->quantity = ($cart->exists ? $cart->quantity : 0) + $quantity;
            $cart->save();
            return;
        }

        $cart = $this->getCart();
        $existing = $cart->firstWhere('product_id', $productId);
        if ($existing) {
            $cart = $cart->map(function ($item) use ($productId, $quantity) {
                if ($item['product_id'] === $productId) {
                    $item['quantity'] = ($item['quantity'] ?? 0) + $quantity;
                }
                return $item;
            });
        } else {
            $cart->push(['product_id' => $productId, 'quantity' => $quantity]);
        }
        session([self::SESSION_KEY => $cart->all()]);
    }

    public function update(int $productId, int $quantity): void
    {
        if (auth()->check()) {
            if ($quantity <= 0) {
                $this->remove($productId);
                return;
            }
            Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->update(['quantity' => $quantity]);
            return;
        }

        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }
        $cart = $this->getCart()->map(function ($item) use ($productId, $quantity) {
            if ($item['product_id'] === $productId) {
                $item['quantity'] = $quantity;
            }
            return $item;
        });
        session([self::SESSION_KEY => $cart->all()]);
    }

    public function remove(int $productId): void
    {
        if (auth()->check()) {
            Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->delete();
            return;
        }

        $cart = $this->getCart()->reject(fn ($item) => ($item['product_id'] ?? null) === $productId);
        session([self::SESSION_KEY => $cart->values()->all()]);
    }

    public function clear(): void
    {
        if (auth()->check()) {
            Cart::where('user_id', auth()->id())->delete();
            return;
        }

        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        if (auth()->check()) {
            return (int) Cart::where('user_id', auth()->id())->sum('quantity');
        }

        return (int) $this->getCart()->sum('quantity');
    }

    public function itemsWithProducts(bool $withCategory = true): Collection
    {
        if (auth()->check()) {
            $items = Cart::where('user_id', auth()->id())
                ->with($withCategory ? ['product.images', 'product.category'] : ['product.images'])
                ->get();

            return $items->map(function (Cart $cart) {
                $product = $cart->product;
                $price = $product ? (float) ($product->discount_price ?? $product->price) : 0;
                return [
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'product' => $product,
                    'price' => $price,
                    'subtotal' => $product ? $price * $cart->quantity : 0,
                ];
            })->filter(fn ($row) => $row['product'] !== null);
        }

        $cart = $this->getCart();
        $productIds = $cart->pluck('product_id')->unique()->filter();
        $products = Product::with($withCategory ? ['images', 'category'] : ['images'])->whereIn('id', $productIds)->get()->keyBy('id');

        return $cart->map(function ($item) use ($products) {
            $product = $products->get($item['product_id']);
            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'product' => $product,
                'price' => $product ? (float) ($product->discount_price ?? $product->price) : 0,
                'subtotal' => $product ? (float) ($product->discount_price ?? $product->price) * $item['quantity'] : 0,
            ];
        })->filter(fn ($row) => $row['product'] !== null);
    }

    public function total(): float
    {
        return app(OrderService::class)->calculateTotal($this->getCart());
    }

    /**
     * Merge session cart into database cart (call after login).
     * Merges quantities for existing items, adds new items, then clears session cart.
     */
    public function mergeSessionCartIntoDatabase(): void
    {
        if (! auth()->check()) {
            return;
        }

        // Merge pending item if customer attempted to add to cart before signing in
        $pendingItem = session()->get('pending_cart_item');
        if (is_array($pendingItem) && ! empty($pendingItem['product_id']) && ! empty($pendingItem['quantity'])) {
            $this->add((int) $pendingItem['product_id'], (int) $pendingItem['quantity']);
            session()->forget('pending_cart_item');
        }

        $sessionCart = collect(session(self::SESSION_KEY, []));
        if ($sessionCart->isEmpty()) {
            return;
        }

        foreach ($sessionCart as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = (int) ($item['quantity'] ?? 0);
            if (! $productId || $quantity < 1) {
                continue;
            }
            $this->add($productId, $quantity);
        }

        session()->forget(self::SESSION_KEY);
    }
}
