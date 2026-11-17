<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDbCommand extends Command
{
    protected $signature = 'app:check-db
                            {--clear-cache : Clear config, cache, and home view cache}';

    protected $description = 'Verify database connection and show record counts (for production troubleshooting).';

    public function handle(): int
    {
        $this->info('--- Database & data check ---');
        $this->newLine();

        $connection = config('database.default');
        $this->line('DB_CONNECTION: ' . $connection);

        try {
            DB::connection()->getPdo();
            $this->info('Database connection: OK');
        } catch (\Throwable $e) {
            $this->error('Database connection FAILED: ' . $e->getMessage());
            $this->newLine();
            $this->line('Fix: Set correct DB_* in .env (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD) and run: php artisan config:clear');
            return self::FAILURE;
        }

        $categories = Category::count();
        $products = Product::count();
        $featured = Product::where('featured', true)->where('status', true)->count();

        $this->table(
            ['Table', 'Count'],
            [
                ['categories', $categories],
                ['products', $products],
                ['products (featured + active)', $featured],
            ]
        );

        if ($categories === 0 && $products === 0) {
            $this->warn('No categories or products found. Import your SQL dump or run: php artisan db:seed');
        }

        if ($this->option('clear-cache')) {
            $this->call('config:clear');
            $this->call('cache:clear');
            \Illuminate\Support\Facades\Cache::forget('home_featured_products');
            \Illuminate\Support\Facades\Cache::forget('home_trending_products');
            \Illuminate\Support\Facades\Cache::forget('home_categories');
            $this->info('Config, cache, and home cache cleared.');
        }

        return self::SUCCESS;
    }
}
