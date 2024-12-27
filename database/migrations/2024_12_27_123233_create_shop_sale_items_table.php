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
        Schema::create('shop_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_sale_id')->constrained('shop_sales');
            $table->foreignId('shop_product_id')->constrained('shop_products');
            $table->unsignedInteger('quantity');
            $table->float('proposer_amount')->nullable();
            $table->float('sold_amount')->nullable();
            $table->float('discount')->default(0);
            $table->enum('status', ['active', 'inactive',])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_sale_items');
    }
};
