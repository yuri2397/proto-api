<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrder extends BaseModel
{
    protected $fillable = [
        'reference',
        'status',
        'date',
        'partner_name',
        'partner_phone',
        'partner_address',
        'station_id',
        'user_id',
    ];

    // boot reference
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->reference = 'SO-' . date('YmdHis') . '-' . rand(1000, 9999);
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
}
