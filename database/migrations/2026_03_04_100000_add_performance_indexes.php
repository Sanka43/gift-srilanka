<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes for query performance. Skips orders.status if already indexed.
     */
    public function up(): void
    {
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['status', 'featured']);
            });
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'Duplicate key name') === false && strpos($msg, 'already exists') === false) {
                throw $e;
            }
        }

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('status');
            });
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'Duplicate key name') === false && strpos($msg, 'already exists') === false) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['status', 'featured']);
            });
        } catch (\Throwable $e) {
            // Index may not exist
        }
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['status']);
            });
        } catch (\Throwable $e) {
            // Index may not exist
        }
    }
};
