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
        Schema::table('shop_sales', function (Blueprint $table) {
            $table->string('ticket_number')->after('payment_method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_sales', function (Blueprint $table) {
            $table->dropColumn('ticket_number');
        });
    }
};
