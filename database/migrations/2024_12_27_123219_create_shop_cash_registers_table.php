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
        Schema::create('shop_cash_registers', function (Blueprint $table) {
            $table->id();
            $table->date('date')->default(now());
            $table->float('starting_balance');
            $table->float('ending_balance')->nullable();
            $table->float('difference')->nullable();
            $table->text('open_remarks')->nullable();
            $table->text('close_remarks')->nullable();
            $table->enum('status', ['open', 'closed', 'cancelled', 'suspended'])->default('open');
            $table->foreignId('station_id')->constrained('stations');
            $table->foreignId('opened_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_cash_registers');
    }
};
