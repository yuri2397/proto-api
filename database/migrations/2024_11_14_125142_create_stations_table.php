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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('pump_operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('contact')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->index('station_id');
            $table->timestamps();
        });

        Schema::create('tanks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['gasoline', 'diesel', 'lpg', 'cng', 'bioethanol', 'biodiesel', 'electric', 'other'])->default('other');
            $table->float('current_quantity')->default(0);
            $table->float('capacity');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->index('station_id');
            $table->timestamps();
        });

        Schema::create('tank_stock_flows', function (Blueprint $table) {
            $table->id();
            $table->float('quantity'); 
            $table->string('type'); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('pumps', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Pump name (gasoil1, super1, etc.)
            $table->enum('status', ['active', 'inactive', 'destroy'])->default('active');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->foreignId('pump_operator_id')->nullable()->constrained()->onDelete('cascade');
            $table->index('station_id');
            $table->timestamps();
        });

        Schema::create('station_cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->float('opening_amount'); // Initial amount in the register
            $table->float('closing_amount')->nullable(); // Final amount at the end of the day
            $table->timestamp('opening_date');
            $table->timestamp('closing_date')->nullable();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->index('station_id');
            $table->timestamps();
        });

        Schema::create('tank_cash_registers', function (Blueprint $table) {
            $table->id();
            $table->float('opening_quantity'); // Initial amount in the register
            $table->float('closing_quantity')->nullable(); // Final amount at the end of the day
            $table->timestamp('opening_date');
            $table->timestamp('closing_date')->nullable();
            $table->foreignId('station_cash_register_id')->constrained()->onDelete('cascade');
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->index('station_cash_register_id');
            $table->index('tank_id');
            $table->timestamps();
        });

        Schema::create('pump_cash_registers', function (Blueprint $table) {
            $table->id();
            $table->float('opening_quantity');
            $table->float('closing_quantity')->nullable();
            $table->timestamp('opening_date');
            $table->timestamp('closing_date')->nullable();
            $table->foreignId('station_cash_register_id')->constrained()->onDelete('cascade');
            $table->foreignId('pump_id')->constrained()->onDelete('cascade');
            $table->foreignId('pump_operator_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->float('volume');
            $table->float('amount');
            $table->timestamp('sale_date');
            $table->foreignId('pump_operator_id')->constrained()->onDelete('cascade');
            $table->foreignId('pump_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_cash_registers')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->float('amount'); // Amount deposited or withdrawn
            $table->string('type'); // 'deposit' or 'withdrawal'
            $table->timestamp('date');
            $table->foreignId('station_cash_registers')->constrained()->onDelete('cascade');
            $table->foreignId('pump_operator_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->float('volume_difference')->nullable();
            $table->float('losses')->nullable();
            $table->foreignId('station_cash_register_id')->constrained()->onDelete('cascade');
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('station_product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('reference')->unique();
            $table->timestamps();
        });

        Schema::create('station_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->double('quantity');
            $table->double('price');
            $table->string('type');
            $table->foreignId('station_product_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('station_product_inventories', function (Blueprint $table) {
            $table->id();
            $table->double('quantity');
            $table->double('price');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
        Schema::dropIfExists('pump_operators');
        Schema::dropIfExists('tanks');
        Schema::dropIfExists('tank_stock_flows');
        Schema::dropIfExists('pumps');
        Schema::dropIfExists('station_cash_registers');
        Schema::dropIfExists('tank_cash_registers');
        Schema::dropIfExists('pump_cash_registers');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('cash_flows');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('station_products');
    }
};
