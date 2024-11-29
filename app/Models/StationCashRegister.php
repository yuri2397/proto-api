<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StationCashRegister extends BaseModel
{
    protected $fillable = ['station_id', 'reference', 'opening_amount', 'closing_amount', 'opening_date', 'closing_date'];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function tankCashRegisters(): HasMany
    {
        return $this->hasMany(TankCashRegister::class);
    }

    public function pumpCashRegisters(): HasMany
    {
        return $this->hasMany(PumpCashRegister::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
