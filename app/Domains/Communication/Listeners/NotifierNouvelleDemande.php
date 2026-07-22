<?php

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Models\Notification;
use App\Domains\Reservation\Events\DemandeReservationCreee;

/**
 * Domaine Communication — voir ARCHITECTURE.md §9/§12, UML.md §5.1.
 */
final class NotifierNouvelleDemande
{
    public function handle(DemandeReservationCreee $event): void
    {
        Notification::create([
            'user_id' => $event->reservation->reservable->partner->user_id,
            'type' => 'nouvelle_demande',
            'payload' => ['reservation_id' => $event->reservation->id],
        ]);
    }
}
