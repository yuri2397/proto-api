<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TankCashRegister extends BaseModel
{
    protected $fillable = ['station_cash_register_id', 'tank_id', 'opening_quantity', 'closing_quantity', 'opening_date', 'closing_date'];

    public function stationCashRegister(): BelongsTo
    {
        return $this->belongsTo(StationCashRegister::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
