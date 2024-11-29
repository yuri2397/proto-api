<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends BaseModel
{
    protected $fillable = ['cash_register_id', 'tank_id', 'volume_difference', 'losses'];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(StationCashRegister::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
