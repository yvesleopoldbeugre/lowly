<?php

namespace App\Domains\Communication\Listeners;

use App\Domains\Communication\Models\Notification;
use App\Domains\Reservation\Events\ContrePropositionSoumise;

/**
 * Domaine Communication — voir ARCHITECTURE.md §9/§12, UML.md §5.3.
 */
final class NotifierContrePropositionRecue
{
    public function handle(ContrePropositionSoumise $event): void
    {
        Notification::create([
            'user_id' => $event->reservation->client_id,
            'type' => 'contre_proposition_recue',
            'payload' => [
                'reservation_id' => $event->reservation->id,
                'counter_offer_id' => $event->counterOffer->id,
            ],
        ]);
    }
}
