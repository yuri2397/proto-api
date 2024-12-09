<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;  
use App\Models\FuelTruckConfig;

class FuelTruckConfigPart extends BaseModel
{
    use HasFactory;

    const TYPE_DIESEL = 'diesel';
    const TYPE_SUPER = 'super';
    const TYPE_GASOLINE = 'gasoline';
    const TYPE_KEROSENE = 'kerosene';
    const TYPE_LPG = 'lpg';
    const TYPE_CNG = 'cng';
    const TYPE_BIOETHANOL = 'bioethanol';
    const TYPE_BIODIESEL = 'biodiesel';
    const TYPE_ELECTRIC = 'electric';
    const TYPE_OTHER = 'other';

    protected $fillable = [
        'fuel_truck_config_id',
        'quantity',
        'capacity',
        'type',
        'name',
        'number',
        'station_id',
        'received_quantity',
        'quantity_before_delivery',
        'quantity_after_delivery',
        'quantity_difference',
        'tank_id'
    ];

    // fuel_truck_config_id
    public function fuelTruckConfig(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruckConfig::class);
    }

    // station_id
    public function station(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    // tank_id
    public function tank(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
