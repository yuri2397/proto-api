<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_order_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->datetime('date');
            $table->unsignedBigInteger('total_amount');
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('shop_product_provider_id')->constrained('shop_product_providers');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_order_invoices');
    }
};
