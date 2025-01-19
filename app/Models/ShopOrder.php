<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrder extends BaseModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'reference',
        'order_number',
        'status',
        'date',
        'station_id',
        'user_id',
        'shop_product_provider_id',
        'shop_order_invoice_id',
    ];

    protected $appends = [
        'totalSellingPrice',
        'totalBuyingPrice',
        'totalProductsItems',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // boot reference
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->reference = 'SO-' . date('Ymd') . rand(1000, 9999);
        });
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // shop order items
    public function shopOrderItems()
    {
        return $this->hasMany(ShopOrderItem::class);
    }
    
    // shop order invoice
    public function shopOrderInvoice()
    {
        return $this->belongsTo(ShopOrderInvoice::class);
    }

    // total selling price
    public function getTotalSellingPriceAttribute()
    {
        $sum = 0;
        foreach ($this->shopOrderItems as $shopOrderItem) {
            $sum += $shopOrderItem->selling_price * $shopOrderItem->quantity;
        }
        return $sum;
    }

    // total buying price
    public function getTotalBuyingPriceAttribute()
    {
        $sum = 0;
        foreach ($this->shopOrderItems as $shopOrderItem) {
            $sum += $shopOrderItem->buying_price * $shopOrderItem->quantity;
        }
        return $sum;
    }

    // count products items
    public function getTotalProductsItemsAttribute()
    {
        return $this->shopOrderItems->count();
    }

    // shop order provider
    public function shopProductProvider()
    {
        return $this->belongsTo(ShopProductProvider::class);
    }
}
