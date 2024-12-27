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
        Schema::create('shop_product_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reference')->nullable()->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_product_sections');
    }
};
