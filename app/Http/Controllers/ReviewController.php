<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CacheService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private readonly CacheService $cacheService
    ) {}

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($product->reviews()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        $product->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $product->updateRatingFromReviews();
        $this->cacheService->invalidateProductRelated();

        return back()->with('success', 'Review added');
    }
}
