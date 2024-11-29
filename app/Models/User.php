<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    protected string $guard_name = 'api';

    protected $with = ['permissions'];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'owner_type',
        'owner_id',
    ];

    /**
     * Les attributs qui doivent être masqués pour les tableaux.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relation morphique pour owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

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
