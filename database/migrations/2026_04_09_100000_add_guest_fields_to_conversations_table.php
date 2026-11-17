<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->uuid('guest_token')->nullable()->unique()->after('customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique(['guest_token']);
            $table->dropColumn('guest_token');
            $table->dropForeign(['customer_id']);
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
