<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('occasion')->nullable()->after('featured');
            $table->string('recipient')->nullable()->after('occasion');
            $table->string('delivery_type')->nullable()->after('recipient');
            $table->decimal('rating', 2, 1)->nullable()->after('delivery_type');
            $table->boolean('in_stock')->default(true)->after('rating');
            $table->boolean('is_on_sale')->default(false)->after('in_stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['occasion', 'recipient', 'delivery_type', 'rating', 'in_stock', 'is_on_sale']);
        });
    }
};
