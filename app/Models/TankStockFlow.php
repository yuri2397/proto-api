<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TankStockFlow extends BaseModel
{
    protected $fillable = ['tank_id', 'quantity', 'type', 'user_id', 'updated_at'];

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
