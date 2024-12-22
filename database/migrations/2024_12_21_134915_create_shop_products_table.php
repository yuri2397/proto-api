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
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reference')->nullable()->unique();
            $table->string('ean13')->nullable();
            $table->string('description')->nullable();
            $table->float('default_selling_price')->nullable();
            $table->float('default_buying_price')->nullable();
            $table->integer('quantity')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_product_section_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_products');
    }
};
