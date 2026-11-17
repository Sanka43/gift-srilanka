<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        if (! $product->status) {
            abort(404);
        }
        $product->load('images', 'category', 'reviews.user');
        $inWishlist = false;
        if (auth()->check()) {
            try {
                $inWishlist = auth()->user()->wishlists()->where('product_id', $product->id)->exists();
            } catch (QueryException $e) {
                // wishlists table may not exist yet (migration not run); treat as not in wishlist
            }
        }

        $metaDescription = $product->description
            ? Str::limit(strip_tags($product->description), 160)
            : config('seo.default_description');
        $metaImage = $product->images->isNotEmpty()
            ? $product->images->first()->public_url
            : asset(config('seo.default_image'));
        $canonical = route('products.show', $product);

        return view('products.show', compact('product', 'inWishlist', 'metaDescription', 'metaImage', 'canonical'));
    }
}
