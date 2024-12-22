<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pump extends BaseModel
{
    protected $fillable = ['tank_id', 'station_id', 'name', 'status'];

    protected $appends = ['last_closing_quantity'];

    // the is the type of the tank + incremented number
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($pump) {
            $pump->name = $pump->tank->type . ' ' . $pump->tank->pumps->count() + 1;
        });
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    // pumpCashRegister
    public function pumpCashRegister(): HasOne
    {
        return $this->hasOne(PumpCashRegister::class);
    }

    // last closing quantity of the tank of the last cash register
    public function getLastClosingQuantityAttribute()
    {
        return $this->pumpCashRegister->closing_quantity ?? 0;
    }
}
