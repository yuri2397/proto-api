<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelTruckDriver extends Model
{
    use HasFactory;
    
    protected $fillable = ['reference', 'name', 'phone'];

    public static function generateReference(): string
    {
        return 'FTD-' . now()->format('Ymd') . '-' . \Illuminate\Support\Str::random(6);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = self::generateReference();  
        });
    }
}
