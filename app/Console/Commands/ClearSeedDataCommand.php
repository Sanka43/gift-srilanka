<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearSeedDataCommand extends Command
{
    protected $signature = 'app:clear-seed-data
                            {--force : Skip confirmation}
                            {--all-products : Remove ALL products (and then empty categories)}
                            {--all-categories : Remove ALL categories}';

    protected $description = 'Remove old seeded/demo products and categories from the database (e.g. after replacing seeders on host). Run once then use cache:clear.';

    public function handle(CacheService $cache): int
    {
        if (! $this->option('force') && ! $this->confirm('This will delete seeded/demo products and categories. Continue?')) {
            return self::FAILURE;
        }

        $productSlugs = ['teddy-bear', 'greeting-card', 'chocolate-box', 'xmas-tree-decor'];
        $categorySlugs = ['birthday', 'anniversary', 'christmas'];

        if ($this->option('all-products')) {
            $products = Product::all();
            $count = $products->count();
            foreach ($products as $product) {
                $product->delete();
            }
            $this->info("Deleted {$count} product(s).");
        } else {
            $products = Product::whereIn('slug', $productSlugs)
                ->orWhere('name', 'like', 'demo%')
                ->get();
            $count = $products->count();
            foreach ($products as $product) {
                $product->delete();
            }
            if ($count > 0) {
                $this->info("Deleted {$count} seeded/demo product(s).");
            } else {
                $this->comment('No seeded/demo products found to delete.');
            }
        }

        if ($this->option('all-categories')) {
            $categories = Category::all();
            $deleted = 0;
            foreach ($categories as $cat) {
                if ($cat->products()->count() === 0) {
                    $cat->delete();
                    $deleted++;
                }
            }
            $this->info("Deleted {$deleted} empty categor(ies).");
        } else {
            $categories = Category::whereIn('slug', $categorySlugs)->get();
            $deleted = 0;
            foreach ($categories as $cat) {
                if ($cat->products()->count() === 0) {
                    $cat->delete();
                    $deleted++;
                } else {
                    $this->warn("Category \"{$cat->name}\" (slug: {$cat->slug}) still has products; not deleted.");
                }
            }
            if ($deleted > 0) {
                $this->info("Deleted {$deleted} seeded categor(ies).");
            } else {
                $this->comment('No seeded categories found to delete (or they still have products).');
            }
        }

        $cache->invalidateProductRelated();
        $cache->invalidateCategoryRelated();
        $this->info('Homepage cache cleared. Done.');

        return self::SUCCESS;
    }
}
