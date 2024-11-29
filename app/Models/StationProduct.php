<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StationProduct extends BaseModel
{
    protected $fillable = ['name', 'description', 'quantity', 'price', 'type', 'station_product_category_id'];

    public function stationProductCategory(): BelongsTo
    {
        return $this->belongsTo(StationProductCategory::class);
    }
}
