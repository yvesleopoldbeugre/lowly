<?php

namespace App\Domains\Reservation\Events;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Domaine Reservation — voir ARCHITECTURE.md §9, UML.md §5.3.
 */
final class ContrePropositionSoumise
{
    use Dispatchable;

    public function __construct(
        public readonly Reservation $reservation,
        public readonly CounterOffer $counterOffer,
        public readonly User $changedBy,
    ) {
        //
    }
}
