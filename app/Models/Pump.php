<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pump extends BaseModel
{
    protected $fillable = ['tank_id', 'station_id', 'name', 'status'];

    // the is the type of the tank + incremented number
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($pump) {
            $pump->name = $pump->tank->type . ' ' . $pump->tank->pumps->count() + 1;
        });
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
