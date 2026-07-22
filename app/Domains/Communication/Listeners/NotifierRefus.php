<?php

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Models\Notification;
use App\Domains\Reservation\Events\ReservationRefusee;

/**
 * Domaine Communication — voir ARCHITECTURE.md §9/§12, UML.md §5.5.
 * `ReservationRefusee` est émis aussi bien pour un refus direct du
 * partenaire que pour le refus d'une contre-proposition par le client
 * (BUSINESS_RULES.md §6.2) : le destinataire est donc toujours l'autre
 * partie que celle à l'origine du refus.
 */
final class NotifierRefus
{
    public function handle(ReservationRefusee $event): void
    {
        $reservation = $event->reservation;
        $partnerUserId = $reservation->reservable->partner->user_id;

        $recipientId = $event->changedBy?->id === $reservation->client_id
            ? $partnerUserId
            : $reservation->client_id;

        Notification::create([
            'user_id' => $recipientId,
            'type' => 'reservation_refusee',
            'payload' => ['reservation_id' => $reservation->id],
        ]);
    }
}
