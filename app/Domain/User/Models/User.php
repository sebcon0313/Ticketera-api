<?php

namespace App\Domain\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Domain\Role\Models\Role;
use App\Domain\Event\Models\Event;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'is_active',
        'email_verified_at',
    ];
                                        
    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /* public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    } */

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    // Funcion para verificar el tipo de usuario por su rol
    public function hasRole($slug): bool
    {
        // Carga la relación si no está cargada
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        
        return $this->role?->slug === $slug;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
