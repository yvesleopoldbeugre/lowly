<?php

namespace App\Domains\Reservation\Models;

use App\Support\Casts\PostgresDateRange;
use Database\Factories\CounterOfferFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Domaine Reservation — voir DATABASE.md §8.3 et BUSINESS_RULES.md §6.
 */
class CounterOffer extends Model
{
    /** @use HasFactory<CounterOfferFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'original_reservation_id',
        'proposed_reservable_type',
        'proposed_reservable_id',
        'proposed_period',
        'status',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'proposed_period' => PostgresDateRange::class,
            'expires_at' => 'datetime',
        ];
    }

    public function originalReservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'original_reservation_id');
    }

    public function proposedReservable(): MorphTo
    {
        return $this->morphTo(null, 'proposed_reservable_type', 'proposed_reservable_id');
    }

    public function isExpired(): bool
    {
        return $this->status === 'expiree' || ($this->status === 'en_attente' && $this->expires_at->isPast());
    }

    protected static function newFactory(): CounterOfferFactory
    {
        return CounterOfferFactory::new();
    }
}
