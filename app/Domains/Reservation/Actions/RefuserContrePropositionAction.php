<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Reservation\Events\ReservationRefusee;
use App\Domains\Reservation\Exceptions\CounterOfferAlreadyAnsweredException;
use App\Domains\Reservation\Exceptions\CounterOfferExpiredException;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;

/**
 * Domaine Reservation — voir API_GUIDE.md §10
 * (`.../counter-offers/{offerId}/reject`), BUSINESS_RULES.md §6.2
 * ("clôture définitivement le cycle de la demande initiale") et UML.md §6.
 */
final class RefuserContrePropositionAction
{
    public function executer(CounterOffer $counterOffer): Reservation
    {
        if ($counterOffer->isExpired()) {
            throw new CounterOfferExpiredException;
        }

        if ($counterOffer->status !== 'en_attente') {
            throw new CounterOfferAlreadyAnsweredException;
        }

        $counterOffer->update(['status' => 'refusee']);

        $original = $counterOffer->originalReservation;
        $previousStatus = $original->status;

        $original->update(['status' => 'refusee']);

        ReservationRefusee::dispatch($original, $previousStatus, $original->client);

        return $original;
    }
}
