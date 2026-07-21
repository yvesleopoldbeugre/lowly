<?php

namespace App\Domains\Communication\Models;

use App\Domains\Identity\Models\User;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Domaine Communication — voir DATABASE.md §9.1 et ARCHITECTURE.md §12.
 *
 * Modèle métier propre à LOWLY, distinct de la table de notifications
 * polymorphe standard de Laravel (voir la remarque sur User::notifications()).
 */
class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory, HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'type',
        'payload',
        'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }
}
