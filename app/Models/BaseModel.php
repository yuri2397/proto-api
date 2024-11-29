<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    /**
     * Convert the model's attributes to an array with camelCase keys.
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = parent::toArray();

        // Transform the keys to camelCase
        $camelCaseAttributes = [];
        foreach ($attributes as $key => $value) {
            $camelCaseAttributes[Str::camel($key)] = $value;
        }

        return $camelCaseAttributes;
    }
}
