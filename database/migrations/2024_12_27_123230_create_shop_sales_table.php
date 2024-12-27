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
        Schema::create('shop_sales', function (Blueprint $table) {
            $table->id();
            $table->date('date')->default(now());
            $table->float('total_amount')->comment('total amount of the sale');
            $table->float('given_amount')->comment('amount given by the customer');
            $table->float('returned_amount')->nullable()->comment('amount returned to the customer');
            $table->float('total_discount')->default(0)->comment('total discount of the sale');
            $table->string('payment_method')->default('cash')->comment('payment method of the sale');
            $table->enum('status', ['pending', 'paid', 'cancelled', 'suspended',])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('station_id')->constrained('stations');
            $table->foreignId('cash_register_id')->constrained('shop_cash_registers');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_sales');
    }
};
