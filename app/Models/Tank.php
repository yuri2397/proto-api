<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Tank extends BaseModel
{
    const STATUS_ACTIVE  = 'active';
    const STATUS_INACTIVE = 'inactive';
    const GASOLINE_TYPE = 'gasoline';
    const DIESEL_TYPE = 'diesel';

    protected $fillable = ['station_id','name', 'type', 'current_quantity', 'capacity', 'status'];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function tankStockFlows(): HasMany
    {
        return $this->hasMany(TankStockFlow::class);
    }

    public function pumps(): HasMany
    {
        return $this->hasMany(Pump::class);
    }
}
