<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopCashRegister extends BaseModel
{
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SUSPENDED = 'suspended';
    
    const STATUS_CHOICES = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
        self::STATUS_CANCELLED,
        self::STATUS_SUSPENDED,
    ];
 
    protected $fillable = [
        'date',
        'starting_balance',
        'ending_balance',
        'difference',
        'open_remarks',
        'close_remarks',
        'station_id',
        'opened_by',
        'closed_by',
        'status',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(ShopSale::class);
    }

}
