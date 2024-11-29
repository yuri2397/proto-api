<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class StationProductCategory extends BaseModel
{
    protected $fillable = ['name', 'reference', 'description'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($category) {
            $category->reference = 'CAT-' . strtoupper(uniqid());
        });
    }

    public function stationProducts(): HasMany
    {
        return $this->hasMany(StationProduct::class);
    }
}
