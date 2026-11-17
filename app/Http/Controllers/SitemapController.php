<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(url(route('home')))->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create(url(route('shop.index')))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        Category::where('status', true)->get()->each(function (Category $category) use ($sitemap) {
            $sitemap->add(
                Url::create(url(route('shop.index', ['category' => $category->slug])))
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        });

        Product::where('status', true)->get()->each(function (Product $product) use ($sitemap) {
            $sitemap->add(
                Url::create(url(route('products.show', $product)))
                    ->setLastModificationDate($product->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        });

        return $sitemap->toResponse(request());
    }
}
