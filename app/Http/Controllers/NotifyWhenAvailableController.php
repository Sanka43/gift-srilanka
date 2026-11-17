<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifyWhenAvailableController extends Controller
{
    /**
     * Store a notification request for when the product is back in stock.
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        $email = $request->input('email') ?: (auth()->check() ? auth()->user()->email : null);
        if ($email) {
            $request->merge(['email' => $email]);
        }

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');

        StockNotification::firstOrCreate(
            [
                'product_id' => $product->id,
                'email' => $email,
            ],
            ['notified_at' => null]
        );

        return response()->json([
            'message' => "We'll notify you when this item is back in stock.",
        ]);
    }
}
