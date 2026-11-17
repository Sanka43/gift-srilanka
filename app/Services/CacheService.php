<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const TTL_SECONDS = 3600; // 1 hour
    private const KEY_FEATURED = 'home_featured_products';
    private const KEY_TRENDING = 'home_trending_products';
    private const KEY_CATEGORIES = 'home_categories';

    public function getFeaturedProducts()
    {
        return Cache::remember(self::KEY_FEATURED, self::TTL_SECONDS, function () {
            return Product::with('images', 'category')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where('featured', true)
                ->where('status', true)
                ->latest()
                ->take(8)
                ->get();
        });
    }

    public function getTrendingProducts()
    {
        return Cache::remember(self::KEY_TRENDING, self::TTL_SECONDS, function () {
            return Product::with('images', 'category')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where('status', true)
                ->latest()
                ->take(6)
                ->get();
        });
    }

    public function getHomeCategories()
    {
        return Cache::remember(self::KEY_CATEGORIES, self::TTL_SECONDS, function () {
            return Category::query()
                ->where('status', true)
                ->whereNull('parent_id')
                ->orderBy('name')
                ->take(12)
                ->get();
        });
    }

    public function invalidateProductRelated(): void
    {
        Cache::forget(self::KEY_FEATURED);
        Cache::forget(self::KEY_TRENDING);
    }

    public function invalidateCategoryRelated(): void
    {
        Cache::forget(self::KEY_CATEGORIES);
        Cache::forget('navbar_categories');
        Cache::forget('footer_categories');
    }
}
