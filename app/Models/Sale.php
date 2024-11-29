<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends BaseModel
{
    protected $fillable = ['pump_operator_id', 'pump_id', 'station_cash_register_id', 'volume', 'amount', 'sale_date'];

    public function pumpOperator(): BelongsTo
    {
        return $this->belongsTo(PumpOperator::class);
    }

    public function pump(): BelongsTo
    {
        return $this->belongsTo(Pump::class);
    }

    public function stationCashRegister(): BelongsTo
    {
        return $this->belongsTo(StationCashRegister::class);
    }
}
