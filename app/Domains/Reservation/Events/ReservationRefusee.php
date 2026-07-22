<?php

namespace App\Domains\Reservation\Events;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Domaine Reservation — voir ARCHITECTURE.md §9, UML.md §5.5. Émis aussi
 * bien pour un refus direct (sans alternative) que pour le refus d'une
 * contre-proposition par le client (clôture définitive du cycle, voir
 * BUSINESS_RULES.md §6.2).
 */
final class ReservationRefusee
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
