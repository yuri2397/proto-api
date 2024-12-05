<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FuelTruckConfigPart;

class FuelTruckConfig extends Model
{
    use HasFactory;
    // fillable
    protected $fillable = ['reference', 'fuel_truck_id', 'total_quantity', 'total_amount', 'description'];
    // fuel truck
    public function fuelTruck(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruck::class);
    }
    // fuel truck config parts
    public function fuelTruckConfigParts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FuelTruckConfigPart::class);
    }
    // station fuel orders
    public function stationFuelOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StationFuelOrder::class);
    }
}
