<?php

namespace App\Domains\Reservation\Policies;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Exceptions\ReservationNotOwnedException;
use App\Domains\Reservation\Models\Reservation;

/**
 * Domaine Reservation — voir docs/engineering/10-security-guidelines.md §4.
 */
final class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->client_id
            || $user->id === $reservation->reservable->partner->user_id;
    }

    /**
     * Jette directement l'exception métier `reservation_not_owned`
     * (docs/engineering/09-api-guidelines.md §7) plutôt que de renvoyer
     * `false`, pour porter ce code spécifique au lieu du `forbidden`
     * générique produit par un simple échec d'autorisation.
     */
    public function respond(User $user, Reservation $reservation): bool
    {
        if ($user->id !== $reservation->reservable->partner->user_id) {
            throw new ReservationNotOwnedException;
        }

        return true;
    }
}
