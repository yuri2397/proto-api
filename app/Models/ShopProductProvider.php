<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class   ShopProductProvider extends  BaseModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUS_LIST = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE
    ];

    protected $fillable = [
        'name',
        'phone',
        'address',
        'email',
        'ninea',
        'rccm',
        'contact_person',
        'contact_person_phone',
        'contact_person_email',
        'status'
    ];

    // shop orders
    public function shopOrders()
    {
        return $this->hasMany(ShopOrder::class);
    }
}
