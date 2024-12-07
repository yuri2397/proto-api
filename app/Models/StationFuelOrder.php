<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FuelTruckConfig;

class StationFuelOrder extends Model
{
    use HasFactory;

    const STATUS_INITIATED = 'initiated';
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_ON_DELIVERY = 'on_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED = 'canceled';

    protected $fillable = ['fuel_truck_config_id', 'reference', 'status', 'data'];

    public static function generateReference(): string
    {
        return 'SFO-' . now()->format('Ymd') . '-' . \Illuminate\Support\Str::random(6);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = self::generateReference();
        });
    }

    public function fuelTruckConfig(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FuelTruckConfig::class);
    }

    public function stationFuelOrderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StationFuelOrderItem::class);
    }

   // count total station_id
   public function totalStation(): int
   {
        return $this->stationFuelOrderItems->count();
   }
}
