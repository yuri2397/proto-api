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
        // truck 
        Schema::create('fuel_trucks', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('matricule')->unique();
            $table->string('transporter_name')->nullable();
            $table->timestamps();
        });

        // truck driver
        Schema::create('fuel_truck_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });


        Schema::create('fuel_truck_configs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->float('total_quantity')->nullable();
            $table->float('total_amount')->nullable();
            $table->enum('status', ['initiated', 'pending', 'confirmed', 'on_delivery', 'delivered', 'canceled'])->default('initiated');
            $table->json('data')->nullable();
            $table->foreignId('fuel_truck_id')->constrained()->onDelete('cascade');
            $table->foreignId('fuel_truck_driver_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('fuel_truck_config_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('number')->nullable();
            $table->float('quantity')->nullable();
            $table->float('capacity')->nullable();
            $table->string('description')->nullable();
            $table->enum('type', ['gasoline', 'diesel', 'super', 'lpg', 'cng', 'bioethanol', 'biodiesel', 'electric', 'other'])->default('other');
            $table->foreignId('fuel_truck_config_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('tank_id')->nullable()->constrained()->onDelete('cascade');
            $table->float('quantity_before_delivery')->nullable();
            $table->float('quantity_after_delivery')->nullable();
            $table->float('quantity_difference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_fuel_orders');
        Schema::dropIfExists('fuel_truck_config_parts');
        Schema::dropIfExists('fuel_truck_configs');

    }
};
