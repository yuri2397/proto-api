<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PumpCashRegister extends BaseModel
{
    protected $fillable = ['station_cash_register_id', 'pump_id', 'pump_operator_id', 'opening_quantity', 'closing_quantity', 'opening_date', 'closing_date'];

    public function stationCashRegister(): BelongsTo
    {
        return $this->belongsTo(StationCashRegister::class);
    }

    public function pump(): BelongsTo
    {
        return $this->belongsTo(Pump::class);
    }

    public function pumpOperator(): BelongsTo
    {
        return $this->belongsTo(PumpOperator::class);
    }

}
