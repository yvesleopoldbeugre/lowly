<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Events\ReservationRefusee;
use App\Domains\Reservation\Models\Reservation;

/**
 * Domaine Reservation — voir API_GUIDE.md §11
 * (`POST /partner/reservations/{id}/reject`), BUSINESS_RULES.md §5.2
 * ("refus sans alternative — cycle clos") et UML.md §5.5.
 */
final class RefuserReservationAction
{
    public function executer(Reservation $reservation, User $changedBy): Reservation
    {
        $previousStatus = $reservation->status;

        $reservation->update(['status' => 'refusee']);

        ReservationRefusee::dispatch($reservation, $previousStatus, $changedBy);

        return $reservation;
    }
}
