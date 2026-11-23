<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('payment_status')->index();
            $table->string('payment_gateway')->nullable()->after('transaction_id');
            $table->json('payment_meta')->nullable()->after('payment_gateway');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->dropColumn(['transaction_id', 'payment_gateway', 'payment_meta']);
        });
    }
};
