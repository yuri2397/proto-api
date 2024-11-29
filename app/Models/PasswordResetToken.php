<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, $email)
 */
class PasswordResetToken extends BaseModel
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'password_reset_tokens';

    /**
     * La clé primaire de la table.
     *
     * @var string
     */
    protected $primaryKey = 'email';

    /**
     * Indique si le modèle doit être horodaté.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    /**
     * Cast des attributs en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
