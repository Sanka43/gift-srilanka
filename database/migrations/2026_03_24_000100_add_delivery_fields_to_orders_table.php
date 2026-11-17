<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('district');
            $table->time('delivery_time')->nullable()->after('delivery_date');
            $table->text('special_note')->nullable()->after('delivery_time');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_date', 'delivery_time', 'special_note']);
        });
    }
};
