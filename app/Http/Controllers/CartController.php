<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart
    ) {}

    public function index(): View
    {
        $items = $this->cart->itemsWithProducts();
        $subtotal = $this->cart->total();
        $deliveryCharge = (float) config('shop.delivery_charge', 350);
        $discount = 0;
        $total = round($subtotal + $deliveryCharge - $discount, 2);
        return view('cart.index', compact('items', 'subtotal', 'deliveryCharge', 'discount', 'total'));
    }

    public function add(AddCartItemRequest $request, Product $product): RedirectResponse
    {
        if (! auth()->check()) {
            $quantity = (int) $request->validated('quantity', 1);
            $quantity = max(1, min(99, $quantity));
            session()->put('pending_cart_item', [
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
            return redirect()->route('register')
                ->with('info', 'Please register to add items to cart.');
        }

        $quantity = (int) $request->validated('quantity', 1);
        $quantity = max(1, min(99, $quantity));
        $canAdd = $this->cart->canAddQuantity($product->id, $quantity);
        if (! $canAdd['ok']) {
            return back()->with('error', $canAdd['message']);
        }
        $this->cart->add($product->id, $quantity);
        if ($request->input('redirect') === 'checkout' || $request->boolean('buy_now')) {
            return redirect()->route('checkout.index')->with('success', 'Item added. Complete your purchase.');
        }
        return back()->with('success', 'Item added to cart.');
    }

    public function update(UpdateCartItemRequest $request, Product $product): RedirectResponse
    {
        $quantity = (int) $request->validated('quantity');
        $canSet = $this->cart->canSetQuantity($product->id, $quantity);
        if (! $canSet['ok']) {
            return redirect()->route('cart.index')->with('error', $canSet['message']);
        }
        $this->cart->update($product->id, $quantity);
        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $this->cart->remove($product->id);
        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }
}
