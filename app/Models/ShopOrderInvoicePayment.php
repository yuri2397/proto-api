<?php

namespace App\Models;

use App\Models\BaseModel;

class ShopOrderInvoicePayment extends BaseModel
{
    protected $fillable = [
        'amount',
        'payment_date',
        'payment_method',
        'payment_reference',
        'status',
        'user_id',
        'shop_order_invoice_id',
        'data',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'data' => 'json',
    ];

    public function shopOrderInvoice()
    {
        return $this->belongsTo(ShopOrderInvoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

