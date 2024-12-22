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
        Schema::create('shop_product_flows', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['sale', 'order', 'stock_in', 'stock_out', 'stock_return', 'stock_correction', 'stock_adjustment'])->default('sale');
            $table->float('quantity')->default(0);
            // quantity before the flow
            $table->float('quantity_before')->default(0);
            // quantity after the flow
            $table->float('quantity_after')->default(0);
            //data json
            $table->json('data')->nullable();
            $table->foreignId('shop_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_product_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_product_flows');
    }
};
