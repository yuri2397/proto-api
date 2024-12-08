<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StationFuelOrderItem extends BaseModel
{
    use HasFactory;

    protected $fillable = ['received_quantity', 'station_id', 'fuel_truck_config_part_id', 'tank_id'];

    // station_id
    public function station(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    // station_fuel_order_id
    public function stationFuelOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StationFuelOrder::class);
    }

    // fuel_truck_config_part_id
    public function fuelTruckConfigPart(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruckConfigPart::class);
    }

    // tank_id
    public function tank(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
