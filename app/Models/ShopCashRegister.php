<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopCashRegister extends BaseModel
{
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SUSPENDED = 'suspended';
    
    const STATUS_CHOICES = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
        self::STATUS_CANCELLED,
        self::STATUS_SUSPENDED,
    ];
 
    protected $fillable = [
        'open_date',
        'close_date',
        'starting_balance',
        'ending_balance',
        'difference',
        'open_remarks',
        'close_remarks',
        'station_id',
        'opened_by',
        'closed_by',
        'status',
    ];

    protected $casts = [
        'open_date' => 'datetime',
        'close_date' => 'datetime',
    ];

    // hidden sales
    protected $hidden = ['sales'];

    protected $appends = ['total_sales', 'total_cash_sale', 'total_wave_sale', 'total_om_sale'];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(ShopSale::class, 'cash_register_id');
    }

    public function getTotalSalesAttribute()
    {
        return $this->sales->sum('total_amount');
    }

    public function getTotalCashSaleAttribute(){
        // SQLSTATE[42S22]: Column not found: 1054 Unknown column 'shop_sales.shop_cash_register_id' in 'where clause' (Connection: mysql, SQL: select * from `shop_sales` where `shop_sales`.`shop_cash_register_id` = 2 and `shop_sales`.`shop_cash_register_id` is not null)
        
        return $this->sales->where('payment_method', ShopSale::PAYMENT_METHOD_CASH)->sum('total_amount');
    }

    public function getTotalWaveSaleAttribute(){
        return $this->sales->where('payment_method', ShopSale::PAYMENT_METHOD_WAVE)->sum('total_amount');
    }

    public function getTotalOmSaleAttribute(){
        return $this->sales->where('payment_method', ShopSale::PAYMENT_METHOD_OM)->sum('total_amount');
    }

}
