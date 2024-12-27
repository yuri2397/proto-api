<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopSaleItem extends BaseModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUS_CHOICES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    protected $fillable = [
        'shop_sale_id',
        'shop_product_id',
        'quantity',
        'proposer_amount',
        'sold_amount',
        'discount',
        'status',
    ];

    public function shopSale(): BelongsTo
    {
        return $this->belongsTo(ShopSale::class);
    }

    public function shopProduct(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class);
    }

}

