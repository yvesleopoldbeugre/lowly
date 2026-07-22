<?php

namespace App\Domains\Reservation\Events;

use App\Domains\Reservation\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Domaine Reservation — voir ARCHITECTURE.md §8.1 et §9, UML.md §5.1.
 */
final class DemandeReservationCreee
{
    use Dispatchable;

    public function __construct(public readonly Reservation $reservation)
    {
        //
    }
}
