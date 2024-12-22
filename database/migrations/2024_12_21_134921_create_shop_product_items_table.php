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
        Schema::create('shop_product_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('reference')->nullable()->unique();
            $table->string('ean13')->nullable();
            $table->float('selling_price')->nullable();
            $table->float('buying_price')->nullable();
            $table->integer('quantity')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('expiration_date')->nullable();
            $table->foreignId('shop_product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_product_items');
    }
};
