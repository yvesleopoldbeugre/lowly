<?php

namespace App\Domains\Reservation\Policies;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\CounterOffer;

/**
 * Domaine Reservation — voir docs/engineering/10-security-guidelines.md §4.
 */
final class CounterOfferPolicy
{
    public function respond(User $user, CounterOffer $counterOffer): bool
    {
        return $user->id === $counterOffer->originalReservation->client_id;
    }
}
