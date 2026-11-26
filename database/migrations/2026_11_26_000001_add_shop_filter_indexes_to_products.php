<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $indexes = [
        'products_status_created_at_index' => ['status', 'created_at'],
        'products_status_category_id_index' => ['status', 'category_id'],
        'products_status_occasion_index' => ['status', 'occasion'],
        'products_status_recipient_index' => ['status', 'recipient'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $name => $columns) {
            if (! Schema::hasIndex('products', $name)) {
                Schema::table('products', fn (Blueprint $t) => $t->index($columns, $name));
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->indexes) as $name) {
            if (Schema::hasIndex('products', $name)) {
                Schema::table('products', fn (Blueprint $t) => $t->dropIndex($name));
            }
        }
    }
};
