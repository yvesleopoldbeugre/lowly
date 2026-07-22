<?php

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Models\Notification;
use App\Domains\Reservation\Events\ContrePropositionExpiree;

/**
 * Domaine Communication — voir ARCHITECTURE.md §9/§12, UML.md §5.4. Seul
 * événement à notifier les deux parties (client et partenaire).
 */
final class NotifierExpiration
{
    public function handle(ContrePropositionExpiree $event): void
    {
        $reservation = $event->reservation;

        $payload = [
            'reservation_id' => $reservation->id,
            'counter_offer_id' => $event->counterOffer->id,
        ];

        Notification::create([
            'user_id' => $reservation->client_id,
            'type' => 'contre_proposition_expiree',
            'payload' => $payload,
        ]);

        Notification::create([
            'user_id' => $reservation->reservable->partner->user_id,
            'type' => 'contre_proposition_expiree',
            'payload' => $payload,
        ]);
    }
}
