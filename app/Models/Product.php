<?php

namespace App\Models;

class Product extends BaseModel
{
    protected $fillable = ['name', 'status', 'reference', 'ean13', 'category', 'description', 'default_price'];

    // boot reference
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->reference = 'PROD-' . rand(100000, 999999);
        });
    }

    public function shopProducts()
    {
        return $this->hasMany(ShopProduct::class);
    }

}

