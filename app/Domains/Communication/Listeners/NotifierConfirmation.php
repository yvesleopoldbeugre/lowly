<?php

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Models\Notification;
use App\Domains\Reservation\Events\ReservationConfirmee;

/**
 * Domaine Communication — voir ARCHITECTURE.md §9/§12, UML.md §5.2.
 */
final class NotifierConfirmation
{
    public function handle(ReservationConfirmee $event): void
    {
        Notification::create([
            'user_id' => $event->reservation->client_id,
            'type' => 'reservation_confirmee',
            'payload' => ['reservation_id' => $event->reservation->id],
        ]);
    }
}
