<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;


class Station extends BaseModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = ['name', 'location', 'status'];

    public function pumpOperators(): HasMany
    {
        return $this->hasMany(PumpOperator::class);
    }

    public function tanks(): HasMany
    {
        return $this->hasMany(Tank::class);
    }

    public function stationCashRegisters(): HasMany
    {
        return $this->hasMany(StationCashRegister::class)->orderBy('created_at', 'desc');
    }

    public function manager(): MorphOne
    {
        return $this->morphOne(User::class, 'owner');
    }
}
