<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductSection extends BaseModel
{
    protected $fillable = ['name', 'status', 'reference', 'station_id'];

    // boot reference
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->reference = 'SPS-' . date('YmdHis') . rand(100, 999);
        });
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function shopProductItems()
    {
        return $this->hasMany(ShopProductItem::class);
    }
}

