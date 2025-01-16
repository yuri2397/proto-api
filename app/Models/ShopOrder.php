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
        'status',
        'date',
        'station_id',
        'user_id',
        'shop_product_provider_id',
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

    // total selling price
    public function getTotalSellingPriceAttribute()
    {
        return $this->shopOrderItems->sum('selling_price');
    }

    // total buying price
    public function getTotalBuyingPriceAttribute()
    {
        return $this->shopOrderItems->sum('buying_price');
    }

    // count products items
    public function getTotalProductsItemsAttribute()
    {
        return $this->shopOrderItems->count();
    }

    // shop order provider
    public function shopOrderProvider()
    {
        return $this->belongsTo(ShopProductProvider::class);
    }
}
