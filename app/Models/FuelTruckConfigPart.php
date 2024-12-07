<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;  
use App\Models\FuelTruckConfig;

class FuelTruckConfigPart extends Model
{
    use HasFactory;

    const TYPE_DIESEL = 'diesel';
    const TYPE_GASOLINE = 'gasoline';
    const TYPE_KEROSENE = 'kerosene';
    const TYPE_LPG = 'lpg';
    const TYPE_CNG = 'cng';
    const TYPE_BIOETHANOL = 'bioethanol';
    const TYPE_BIODIESEL = 'biodiesel';
    const TYPE_ELECTRIC = 'electric';
    const TYPE_OTHER = 'other';

    protected $fillable = ['fuel_truck_config_id', 'quantity', 'capacity', 'type'];

    public function fuelTruckConfig(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruckConfig::class);
    }

    public function fuelTruck(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruck::class);
    }

    public function fuelTruckDriver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruckDriver::class);
    }
}
