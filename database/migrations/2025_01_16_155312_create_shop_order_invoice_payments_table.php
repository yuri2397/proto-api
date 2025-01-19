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
        Schema::create('shop_order_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('amount');
            $table->datetime('payment_date');
            $table->string('payment_method');
            $table->string('payment_reference');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('shop_order_invoice_id')->constrained('shop_order_invoices');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_order_invoice_payments');
    }
};
