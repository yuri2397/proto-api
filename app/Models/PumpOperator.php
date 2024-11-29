<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PumpOperator extends BaseModel
{
    protected $fillable = ['station_id', 'name', 'contact', 'status'];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function pumps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pump::class);
    }
}
