<?php

namespace App\Domains\Reservation\Controllers\Api;

use App\Domains\Reservation\Actions\AccepterContrePropositionAction;
use App\Domains\Reservation\Actions\RefuserContrePropositionAction;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Resources\ReservationResource;
use App\Http\Controllers\Controller;

/**
 * Domaine Reservation — voir API_GUIDE.md §10, BUSINESS_RULES.md §6
 * (réponse du client à une contre-proposition).
 */
class CounterOfferController extends Controller
{
    public function accept(Reservation $reservation, CounterOffer $counterOffer, AccepterContrePropositionAction $action): ReservationResource
    {
        $this->authorize('respond', $counterOffer);

        return ReservationResource::make($action->executer($counterOffer));
    }

    public function reject(Reservation $reservation, CounterOffer $counterOffer, RefuserContrePropositionAction $action): ReservationResource
    {
        $this->authorize('respond', $counterOffer);

        return ReservationResource::make($action->executer($counterOffer));
    }
}
