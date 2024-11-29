<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlow extends BaseModel
{
    protected $fillable = ['station_cash_register_id', 'pump_operator_id', 'amount', 'type', 'date'];

    public function stationCashRegister(): BelongsTo
    {
        return $this->belongsTo(StationCashRegister::class);
    }

    public function pumpOperator(): BelongsTo
    {
        return $this->belongsTo(PumpOperator::class);
    }
}
