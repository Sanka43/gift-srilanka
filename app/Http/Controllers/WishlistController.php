<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $items = auth()->user()->wishlists()->with('product.images')->latest()->paginate(12);
        return view('wishlist.index', compact('items'));
    }

    public function add(Product $product): RedirectResponse
    {
        Wishlist::firstOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $product->id],
            ['user_id' => auth()->id(), 'product_id' => $product->id]
        );
        return back()->with('success', 'Added to wishlist.');
    }

    public function remove(Product $product): RedirectResponse
    {
        auth()->user()->wishlists()->where('product_id', $product->id)->delete();
        return back()->with('success', 'Removed from wishlist.');
    }
}
