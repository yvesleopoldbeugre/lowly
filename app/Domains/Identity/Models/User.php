<?php

namespace App\Domains\Identity\Models;

use App\Domains\Administration\Models\AdminAction;
use App\Domains\Communication\Models\Notification as LowlyNotification;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\Reservation;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Domaine Identity — voir DATABASE.md §4.1 et UML.md §4.2.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'role',
        'suspended_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function partner(): HasOne
    {
        return $this->hasOne(Partner::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }

    /**
     * Notifications métier LOWLY (table `notifications` custom — voir
     * DATABASE.md §9.1). Redéfinit volontairement la relation `notifications()`
     * fournie par le trait `Notifiable`, qui cible la table de notifications
     * polymorphe standard de Laravel, non utilisée par ce projet.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(LowlyNotification::class);
    }

    public function adminActions(): HasMany
    {
        return $this->hasMany(AdminAction::class, 'admin_id');
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isPartner(): bool
    {
        return $this->role === 'partner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
