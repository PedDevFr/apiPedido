<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    public const ADMIN = 'admin';
    public const VENDEDOR = 'vendedor';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Usuarios que tienen este rol.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Verifica si el rol es admin.
     */
    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    /**
     * Verifica si el rol es vendedor.
     */
    public function isVendedor(): bool
    {
        return $this->name === self::VENDEDOR;
    }
}
