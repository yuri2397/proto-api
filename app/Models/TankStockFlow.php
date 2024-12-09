<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TankStockFlow extends BaseModel
{
    protected $fillable = ['tank_id', 'quantity','previous_quantity', 'after_quantity', 'type', 'user_id', 'updated_at', 'data', 'dataable_type', 'dataable_id'];

    protected $casts = [
        'data' => 'array',
    ];

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
