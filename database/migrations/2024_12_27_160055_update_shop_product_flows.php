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
        Schema::table('shop_product_flows', function (Blueprint $table) {
            // remove shop_product_item_id
            $table->dropForeign(['shop_product_item_id']);
            $table->dropColumn('shop_product_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_product_flows', function (Blueprint $table) {
            // add shop_product_item_id
            $table->foreignId('shop_product_item_id')->constrained()->onDelete('cascade');
        });
    }
};
