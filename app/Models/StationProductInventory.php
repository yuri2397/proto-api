<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StationProductInventory extends BaseModel
{
    protected $fillable = ['quantity', 'price', 'user_id', 'station_product_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stationProduct(): BelongsTo
    {
        return $this->belongsTo(StationProduct::class);
    }
}
