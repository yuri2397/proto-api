<?php

namespace App\Models;

use App\Models\BaseModel;

class ShopProductItem extends BaseModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUS_CHOICES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    protected $fillable = ['name', 'ean13', 'expiration_date', 'status', 'selling_price', 'buying_price', 'shop_product_id', 'quantity'];

    // boot reference
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->reference = 'SPI-' . rand(100000, 999999);
        });
    }

    public function shopProduct()
    {
        return $this->belongsTo(ShopProduct::class);
    }
}
