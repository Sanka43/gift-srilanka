<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->text('address');
            $table->string('district');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_method');
            $table->string('payment_status')->default('pending');
            $table->timestamps();
            $table->index('status');
            // user_id already indexed via foreign key
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
