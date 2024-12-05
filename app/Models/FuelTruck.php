<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class FuelTruck extends Model
{
    use HasFactory;
    // fillable
    protected $fillable = ['reference', 'plate_number', 'status'];
    // owner
    public function owner(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
    // fuel truck config
    public function fuelTruckConfigs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FuelTruckConfig::class);
    }

    // station fuel orders
    public function stationFuelOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StationFuelOrder::class);
    }
}
