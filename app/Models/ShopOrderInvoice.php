<?php

namespace App\Models;

use App\Models\BaseModel;

class ShopOrderInvoice extends BaseModel
{
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_LIST = [
        self::STATUS_UNPAID,
        self::STATUS_PAID,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'reference',
        'date',
        'total_amount',
        'status',
        'user_id',
        'shop_product_provider_id',
        'data', 
    ];

    protected $casts = [
        'date' => 'date',
        'data' => 'json',
    ];

    // Boot reference
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            // Récupérer la dernière référence
            $lastRecord = self::orderBy('id', 'desc')->first();

            if ($lastRecord && $lastRecord->reference) {
                // Extraire la partie numérique de la référence et l'incrémenter
                $lastReferenceNumber = (int) filter_var($lastRecord->reference, FILTER_SANITIZE_NUMBER_INT);
                $newReferenceNumber = $lastReferenceNumber + 1;

                // Générer une nouvelle référence avec le préfixe "FI"
                $model->reference = 'OI' . str_pad($newReferenceNumber, 6, '0', STR_PAD_LEFT);
            } else {
                // Si aucune référence, utiliser la date actuelle
                $model->reference = 'OI' . now()->format('Ymd') . '001'; // Exemple : FI20250116001
            }
        });
    }

    public function shopOrderInvoicePayments()
    {
        return $this->hasMany(ShopOrderInvoicePayment::class);
    }

    public function shopProductProvider()
    {
        return $this->belongsTo(ShopProductProvider::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shopOrders()
    {
        return $this->hasMany(ShopOrder::class);
    }

   
    // scope for status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // scope for search facture number
    public function scopeSearchFactureNumber($query, $factureNumber)
    {
        return $query->where('reference', $factureNumber);
    }

    // scope for search date
    public function scopeSearchDate($query, $date)
    {
        return $query->where('date', $date);
    }
}


