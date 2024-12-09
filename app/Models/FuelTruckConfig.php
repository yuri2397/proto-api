<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FuelTruckConfigPart;

class FuelTruckConfig extends BaseModel
{
    use HasFactory;

    const STATUSES = ['initiated', 'pending', 'confirmed', 'on_delivery', 'delivered', 'canceled'];
    protected $fillable = ['reference', 'total_quantity', 'total_amount', 'description', 'fuel_truck_id', 'fuel_truck_driver_id', 'status', 'data'];


    public static function generateReference(): string
    {
        return 'FTC-' . now()->format('Ymd') . '-' . \Illuminate\Support\Str::random(6);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = self::generateReference();
        });
    }

    // fuel_truck_id
    public function fuelTruck(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruck::class);
    }

    // fuel_truck_driver_id
    public function fuelTruckDriver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruckDriver::class, 'fuel_truck_driver_id');
    }

    // fuel_truck_config_parts
    public function fuelTruckConfigParts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FuelTruckConfigPart::class);
    }
}
