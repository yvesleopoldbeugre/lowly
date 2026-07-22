<?php

namespace App\Domains\Reservation\Events;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Domaine Reservation — voir ARCHITECTURE.md §8.2 et §9, UML.md §5.2/§5.3.
 * Déclenche le blocage automatique du calendrier (Availability) et les
 * notifications (Communication).
 */
final class ReservationConfirmee
{
    use Dispatchable;

    public function __construct(
        public readonly Reservation $reservation,
        public readonly string $previousStatus,
        public readonly ?User $changedBy,
    ) {
        //
    }
}
