<?php

namespace App\Domains\Availability\Models;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\Reservation;
use App\Support\Casts\PostgresDateRange;
use Database\Factories\AvailabilityBlockFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Domaine Availability — voir DATABASE.md §7 et UML.md §4.5.
 *
 * L'invariant de non-chevauchement (un seul blocage actif par bien et par
 * période, voir BUSINESS_RULES.md §7.2) est porté par la contrainte
 * d'exclusion GiST `excl_availability_no_overlap` en base de données, pas
 * par ce modèle : toute tentative de chevauchement doit échouer au niveau
 * SQL, indépendamment de la couche applicative.
 */
class AvailabilityBlock extends Model
{
    /** @use HasFactory<AvailabilityBlockFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'blockable_type',
        'blockable_id',
        'period',
        'origin',
        'reservation_id',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period' => PostgresDateRange::class,
        ];
    }

    public function blockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isFromReservation(): bool
    {
        return $this->origin === 'reservation';
    }

    /**
     * Blocages existants chevauchant `$period` sur un bien donné — utilisé
     * pour la vérification "douce" de disponibilité avant qu'un blocage ne
     * soit réellement posé (BUSINESS_RULES.md §5.1, §6.2), la contrainte
     * d'exclusion GiST restant la seule garantie dure à l'écriture.
     *
     * @param  array{start: mixed, end: mixed}  $period
     */
    public function scopeOverlapping(Builder $query, string $blockableType, string $blockableId, array $period): Builder
    {
        return $query
            ->where('blockable_type', $blockableType)
            ->where('blockable_id', $blockableId)
            ->whereRaw('period && daterange(?, ?)', [$period['start'], $period['end']]);
    }

    protected static function newFactory(): AvailabilityBlockFactory
    {
        return AvailabilityBlockFactory::new();
    }
}
