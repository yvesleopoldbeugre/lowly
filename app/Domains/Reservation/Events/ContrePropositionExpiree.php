<?php

namespace App\Domains\Reservation\Events;

use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Domaine Reservation — voir ARCHITECTURE.md §9, UML.md §5.4. Notifie
 * client et partenaire (seul événement à deux destinataires).
 */
final class ContrePropositionExpiree
{
    use Dispatchable;

    public function __construct(
        public readonly Reservation $reservation,
        public readonly CounterOffer $counterOffer,
    ) {
        //
    }
}
