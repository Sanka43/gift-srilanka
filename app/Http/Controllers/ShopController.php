<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('images', 'category')->withCount('reviews')->withAvg('reviews', 'rating')->where('status', true);

        $seoCategory = null;
        if ($request->filled('category')) {
            $seoCategory = Category::where('slug', $request->category)->where('status', true)->first();
            if ($seoCategory) {
                $categoryIds = collect([$seoCategory->id])->merge(
                    $seoCategory->children()->pluck('id')
                );
                $query->whereIn('category_id', $categoryIds->toArray());
            }
        }

        if ($request->filled('search')) {
            $search = str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $request->search);
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        $occasions = $request->input('occasion', []);
        $occasions = is_array($occasions) ? $occasions : (array) $occasions;
        if (! empty($occasions)) {
            $query->whereIn('occasion', $occasions);
        }

        $recipients = $request->input('recipient', []);
        $recipients = is_array($recipients) ? $recipients : (array) $recipients;
        if (! empty($recipients)) {
            $query->whereIn('recipient', $recipients);
        }

        if ($request->filled('delivery_type')) {
            $query->where('delivery_type', $request->delivery_type);
        }

        if ($request->filled('rating') && is_numeric($request->rating)) {
            $minRating = (float) $request->rating;
            $query->where(function ($q) use ($minRating) {
                $q->where('rating', '>=', $minRating)
                    ->orWhereRaw('(SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE reviews.product_id = products.id) >= ?', [$minRating]);
            });
        }

        // Show out-of-stock products by default; only hide them when "In Stock Only" filter is on
        if ($request->filled('in_stock') && $request->boolean('in_stock')) {
            $query->where('in_stock', true);
        }

        if ($request->filled('on_sale') && $request->boolean('on_sale')) {
            $query->where('is_on_sale', true);
        }

        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [(float) $request->min_price]);
        }
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->whereRaw('COALESCE(discount_price, price) <= ?', [(float) $request->max_price]);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'popular' => $query->orderByDesc('featured')->latest(),
            'price_asc' => $query->orderByRaw('COALESCE(discount_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(discount_price, price) DESC'),
            'rating_desc' => $query->orderByDesc('rating'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('status', true)->orderBy('name')->get();

        // Main categories with children for filter sidebar (real tree from DB)
        $mainCategories = Category::mainCategories()
            ->where('status', true)
            ->with(['children' => fn ($q) => $q->where('status', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        // Category counts (products per category + total) for sidebar badges
        $categoryCounts = [];
        $baseQuery = Product::where('status', true);
        $categoryCounts['all'] = (clone $baseQuery)->count();
        $countsByCategoryId = (clone $baseQuery)
            ->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');
        foreach ($categories as $cat) {
            $categoryCounts[$cat->slug] = (int) ($countsByCategoryId[$cat->id] ?? 0);
        }

        $priceRange = Product::where('status', true)
            ->selectRaw('MIN(COALESCE(discount_price, price)) as min, MAX(COALESCE(discount_price, price)) as max')
            ->first();

        // Recommendations: featured first, then latest (exclude current page results for variety)
        $recommendations = Product::with('images', 'category')->withCount('reviews')->withAvg('reviews', 'rating')->where('status', true)
            ->orderByDesc('featured')
            ->latest()
            ->take(12)
            ->get();

        return view('shop.index', compact('products', 'categories', 'mainCategories', 'priceRange', 'categoryCounts', 'recommendations', 'seoCategory'));
    }
}
