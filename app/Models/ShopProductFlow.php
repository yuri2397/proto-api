<?php

namespace App\Models;

use App\Models\BaseModel;

class ShopProductFlow extends BaseModel
{
    const TYPE_SALE = 'sale';
    const TYPE_ORDER = 'order';
    const TYPE_STOCK_IN = 'stock_in';
    const TYPE_STOCK_OUT = 'stock_out';
    const TYPE_STOCK_RETURN = 'stock_return';
    const TYPE_STOCK_CORRECTION = 'stock_correction';
    const TYPE_STOCK_ADJUSTMENT = 'stock_adjustment';

    const TYPE_LIST = [
        self::TYPE_SALE,
        self::TYPE_ORDER,
        self::TYPE_STOCK_IN,
        self::TYPE_STOCK_OUT,
        self::TYPE_STOCK_RETURN,
        self::TYPE_STOCK_CORRECTION,
        self::TYPE_STOCK_ADJUSTMENT,
    ];

    protected $fillable = [
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'shop_product_id',
        'data',
        'user_id'
    ];

    // cast
    protected $casts = [
        'data' => 'array',
    ];

    public function shopProduct()
    {
        return $this->belongsTo(ShopProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('type', 'like', '%' . $search . '%');
    }
}
