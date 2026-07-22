<?php

namespace App\Domains\Availability\Listeners;

use App\Domains\Availability\Exceptions\AvailabilityBlockOverlapException;
use App\Domains\Reservation\Events\ReservationConfirmee;
use Illuminate\Database\QueryException;

/**
 * Domaine Availability — voir ARCHITECTURE.md §8.2/§9 (listener `L1` de
 * UML.md §5.2), déclenché par `ReservationConfirmee` (Reservation). Bloque
 * automatiquement la période confirmée (BUSINESS_RULES.md §3.4) via le
 * même chemin que `CreateAvailabilityBlock` (contrainte d'exclusion GiST
 * `excl_availability_no_overlap`, capturée ici pour la même raison :
 * garantir la non-duplication même en cas de confirmations concurrentes).
 */
final class BloquerCalendrier
{
    public function handle(ReservationConfirmee $event): void
    {
        $reservation = $event->reservation;

        try {
            $reservation->reservable->availabilityBlocks()->create([
                'period' => $reservation->period,
                'origin' => 'reservation',
                'reservation_id' => $reservation->id,
                'created_by' => $reservation->client_id,
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23P01') {
                throw new AvailabilityBlockOverlapException;
            }

            throw $e;
        }
    }
}
