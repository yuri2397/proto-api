<?php

namespace App\Models;

use App\Models\BaseModel;

class ShopProduct extends BaseModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUS_CHOICES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    protected $fillable = [
        'name',
        'reference',
        'ean13',
        'description',
        'default_selling_price',
        'default_buying_price',
        'status',
        'product_id',
        'station_id',
        'shop_product_section_id'
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

                // Générer une nouvelle référence avec le préfixe "SP"
                $model->reference = 'SP' . str_pad($newReferenceNumber, 6, '0', STR_PAD_LEFT);
            } else {
                // Si aucune référence, utiliser la date actuelle
                $model->reference = 'SP' . now()->format('Ymd') . '001'; // Exemple : SP20250116001
            }
        });
    }

    public function getQuantityAttribute()
    {
        return $this->shopProductItems->sum('quantity');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function shopProductItems()
    {
        return $this->hasMany(ShopProductItem::class);
    }

    // flow
    public function shopProductFlows()
    {
        return $this->hasMany(ShopProductFlow::class);
    }

    public function shopProductSection()
    {
        return $this->belongsTo(ShopProductSection::class);
    }

    public function scopeWith($query, $with)
    {
        return $query->with($with);
    }

    public function scopeWhereStationId($query, $stationId)
    {
        return $query->where('station_id', $stationId);
    }

    public function scopeWhereProductId($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeWhereStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWhereReference($query, $reference)
    {
        return $query->where('reference', $reference);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('reference', 'like', '%' . $search . '%')
            ->orWhereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('ean13', 'like', '%' . $search . '%');
    }
}
