<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrderItem extends BaseModel
{
    protected $fillable = [
        'shop_order_id',
        'shop_product_id',
        'shop_product_item_id',
        'quantity',
        'buying_price',
        'selling_price',
        'tva',
    ];

    public function shopOrder()
    {
        return $this->belongsTo(ShopOrder::class);
    }

    public function shopProduct()
    {
        return $this->belongsTo(ShopProduct::class);
    }

    public function shopProductItem()
    {
        return $this->belongsTo(ShopProductItem::class);
    }
}

