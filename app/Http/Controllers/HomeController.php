<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __construct(
        private CacheService $cache
    ) {}

    public function index(): View
    {
        $categories = $this->cache->getHomeCategories();
        $featuredProducts = $this->cache->getFeaturedProducts();
        $trendingProducts = $this->cache->getTrendingProducts();
        $seoDescription = config('seo.default_description');
        $seoImage = asset(config('seo.default_image'));

        return view('home', compact('categories', 'featuredProducts', 'trendingProducts', 'seoDescription', 'seoImage'));
    }
}
