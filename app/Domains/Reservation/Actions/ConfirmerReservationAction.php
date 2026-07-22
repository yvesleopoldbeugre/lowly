<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Events\ReservationConfirmee;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Support\Facades\DB;

/**
 * Domaine Reservation — voir API_GUIDE.md §11
 * (`POST /partner/reservations/{id}/accept`), BUSINESS_RULES.md §5.2/§5.3
 * et UML.md §5.2. Le blocage calendrier et les notifications sont
 * déclenchés par les listeners de l'événement (ARCHITECTURE.md §9),
 * exécutés de façon synchrone dans la même transaction que la confirmation :
 * si le blocage échoue (chevauchement concurrent), la confirmation elle-même
 * doit être annulée plutôt que de laisser la réservation "confirmée" sans
 * calendrier bloqué.
 */
final class ConfirmerReservationAction
{
    public function executer(Reservation $reservation, User $changedBy): Reservation
    {
        DB::transaction(function () use ($reservation, $changedBy) {
            $previousStatus = $reservation->status;

            $reservation->update(['status' => 'confirmee']);

            ReservationConfirmee::dispatch($reservation, $previousStatus, $changedBy);
        });

        return $reservation;
    }
}
