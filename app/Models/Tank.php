<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Tank extends BaseModel
{
    const STATUS_ACTIVE  = 'active';
    const STATUS_INACTIVE = 'inactive';

    const TYPE_GASOLINE = 'gasoline';
    const TYPE_DIESEL = 'diesel';
    const TYPE_SUPER = 'super';
    const TYPE_LPG = 'lpg';
    const TYPE_CNG = 'cng';
    const TYPE_BIOETHANOL = 'bioethanol';
    const TYPE_BIODIESEL = 'biodiesel';
    const TYPE_ELECTRIC = 'electric';
    const TYPE_OTHER = 'other';


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

    // addQuantity
    public function addQuantity($quantity)
    {
        $this->update(['current_quantity' => $this->current_quantity + $quantity]);
    }
}
