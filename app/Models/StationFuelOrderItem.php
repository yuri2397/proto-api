<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StationFuelOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['received_quantity', 'station_id', 'fuel_truck_config_part_id', 'tank_id'];


    public function stationFuelOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StationFuelOrder::class);
    }

    public function fuelTruckConfigPart(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruckConfigPart::class);
    }

    public function tank(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

}
