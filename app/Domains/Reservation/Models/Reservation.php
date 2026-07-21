<?php

namespace App\Domains\Reservation\Models;

use App\Domains\Identity\Models\User;
use App\Support\Casts\PostgresDateRange;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Domaine Reservation — voir DATABASE.md §8.1 et UML.md §4.6.
 */
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'reservable_type',
        'reservable_id',
        'period',
        'nights_count',
        'total_amount',
        'status',
        'parent_reservation_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period' => PostgresDateRange::class,
            'total_amount' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function reservable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Demande initiale, si cette réservation résulte d'une contre-proposition
     * acceptée — voir BUSINESS_RULES.md §6.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_reservation_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_reservation_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ReservationStatusHistory::class);
    }

    public function counterOffer(): HasOne
    {
        return $this->hasOne(CounterOffer::class, 'original_reservation_id');
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmee';
    }

    protected static function newFactory(): ReservationFactory
    {
        return ReservationFactory::new();
    }
}
