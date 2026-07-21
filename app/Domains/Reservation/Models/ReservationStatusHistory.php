<?php

namespace App\Domains\Reservation\Models;

use App\Domains\Identity\Models\User;
use Database\Factories\ReservationStatusHistoryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Domaine Reservation — voir DATABASE.md §8.2. `changed_at` est géré
 * explicitement par l'application (Listener EnregistrerHistorique — voir
 * ARCHITECTURE.md §8.2), pas par les timestamps automatiques d'Eloquent.
 */
class ReservationStatusHistory extends Model
{
    /** @use HasFactory<ReservationStatusHistoryFactory> */
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'reservation_id',
        'previous_status',
        'new_status',
        'changed_by',
        'changed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    protected static function newFactory(): ReservationStatusHistoryFactory
    {
        return ReservationStatusHistoryFactory::new();
    }
}
