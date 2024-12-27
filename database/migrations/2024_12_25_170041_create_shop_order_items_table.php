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
        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->float('quantity');
            $table->float('buying_price');
            $table->float('selling_price');
            $table->double('tva')->default(0);
            $table->foreignId('shop_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_product_item_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
    }
};
