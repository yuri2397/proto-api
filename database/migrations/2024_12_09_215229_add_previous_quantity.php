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
        Schema::table('tank_stock_flows', function (Blueprint $table) {
            // previous_quantity
            $table->double('previous_quantity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tank_stock_flows', function (Blueprint $table) {
            $table->dropColumn('previous_quantity');
        });
    }
};
