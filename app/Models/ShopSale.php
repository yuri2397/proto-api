<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopSale extends BaseModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SUSPENDED = 'suspended';

    const STATUS_CHOICES = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_CANCELLED,
        self::STATUS_SUSPENDED,
    ];

    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_WAVE = 'wave';
    const PAYMENT_METHOD_OM = 'om';
    const PAYMENT_METHOD_OTHER = 'other';

    const PAYMENT_METHOD_LIST = [
        self::PAYMENT_METHOD_CASH,
        self::PAYMENT_METHOD_WAVE,
        self::PAYMENT_METHOD_OM,
        self::PAYMENT_METHOD_OTHER,
    ];


    protected $fillable = [
        'date',
        'station_id',
        'cash_register_id',
        'user_id',
        'total_amount',
        'given_amount',
        'returned_amount',
        'remarks',
        'status',
        'payment_method',
        'ticket_number',
    ];

    // boot
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->ticket_number = $model->generateTicketNumber();
        });
    }

    public function generateTicketNumber()
    {
        // Récupérer la dernière référence
        $lastRecord = self::where('station_id', $this->station_id)->orderBy('id', 'desc')->first();

        if ($lastRecord && $lastRecord->ticket_number) {
            // supprimer le T- et convertir en int
            $lastTiketNumberValue = intval(substr($lastRecord->ticket_number, 2));
            return 'T-' . ($lastTiketNumberValue + 1);
        } else {
            // Si aucune référence, utiliser la date actuelle
            return 'T-' . 1; // Exemple : T-20250116001
        }
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(ShopCashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shopSaleItems(): HasMany
    {
        return $this->hasMany(ShopSaleItem::class);
    }

    public function scopeSearch($query, $search)
    {
        // search by date user, product name, payment method, status
        return $query->where('payment_method', 'like', '%' . $search . '%')
            ->orWhereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('ticket_number', $search)
            ->orWhereHas('shopSaleItems', function ($query) use ($search) {
                $query->whereHas('shopProduct', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            });
    }
}
