<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class FuelTruck extends BaseModel
{
    use HasFactory;
    // fillable
    protected $fillable = ['reference', 'matricule', 'status'];
    
    // generate reference
    public static function generateReference(): string
    {
        return 'FT-' . now()->format('Ymd') . '-' . \Illuminate\Support\Str::random(6);
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = self::generateReference();
        });
    }

}
