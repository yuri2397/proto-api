<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;  
use App\Models\FuelTruckConfig;

class FuelTruckConfigPart extends Model
{
    use HasFactory;
    // fillable
    protected $fillable = ['reference', 'fuel_truck_config_id', 'quantity', 'capacity', 'type', 'description'];
    // fuel truck config
    public function fuelTruckConfig(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruckConfig::class);
    }
}
