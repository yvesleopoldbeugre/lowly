<?php

namespace App\Domains\Catalogue\Models;

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Contracts\OffreReservable;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\Reservation;
use Database\Factories\ResidenceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Domaine Catalogue — voir DATABASE.md §6.1 et UML.md §4.4.
 */
class Residence extends Model implements OffreReservable
{
    /** @use HasFactory<ResidenceFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'title',
        'description',
        'address',
        'city',
        'capacity',
        'daily_rate',
        'attributes',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'daily_rate' => 'decimal:2',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ResidencePhoto::class);
    }

    public function reservations(): MorphMany
    {
        return $this->morphMany(Reservation::class, 'reservable');
    }

    public function availabilityBlocks(): MorphMany
    {
        return $this->morphMany(AvailabilityBlock::class, 'blockable');
    }

    public function dailyRate(): string
    {
        return (string) $this->daily_rate;
    }

    public function isPublished(): bool
    {
        return $this->status === 'publiee';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'publiee');
    }

    public function scopeInCity(Builder $query, ?string $city): Builder
    {
        return $query->when($city, fn (Builder $q) => $q->where('city', $city));
    }

    public function scopePriceBetween(Builder $query, ?float $min, ?float $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('daily_rate', '>=', $min))
            ->when($max !== null, fn (Builder $q) => $q->where('daily_rate', '<=', $max));
    }

    public function scopeWithCapacity(Builder $query, ?int $capacity): Builder
    {
        return $query->when($capacity, fn (Builder $q) => $q->where('capacity', '>=', $capacity));
    }

    protected static function newFactory(): ResidenceFactory
    {
        return ResidenceFactory::new();
    }
}
