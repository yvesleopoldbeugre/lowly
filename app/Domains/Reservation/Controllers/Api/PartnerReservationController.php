<?php

namespace App\Domains\Reservation\Controllers\Api;

use App\Domains\Reservation\Actions\ConfirmerReservationAction;
use App\Domains\Reservation\Actions\ListPartnerReservations;
use App\Domains\Reservation\Actions\RefuserAvecContrePropositionAction;
use App\Domains\Reservation\Actions\RefuserReservationAction;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Requests\StoreCounterOfferRequest;
use App\Domains\Reservation\Resources\CounterOfferResource;
use App\Domains\Reservation\Resources\ReservationResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Reservation — voir API_GUIDE.md §11, BUSINESS_RULES.md §5.2
 * (traitement d'une demande par le partenaire).
 */
class PartnerReservationController extends Controller
{
    public function index(Request $request, ListPartnerReservations $action): AnonymousResourceCollection
    {
        return ReservationResource::collection(
            $action->executer($request->user()->partner, ['per_page' => $request->integer('per_page')])
        );
    }

    public function accept(Request $request, Reservation $reservation, ConfirmerReservationAction $action): ReservationResource
    {
        $this->authorize('respond', $reservation);

        return ReservationResource::make($action->executer($reservation, $request->user()));
    }

    public function reject(Request $request, Reservation $reservation, RefuserReservationAction $action): ReservationResource
    {
        $this->authorize('respond', $reservation);

        return ReservationResource::make($action->executer($reservation, $request->user()));
    }

    public function counterOffer(StoreCounterOfferRequest $request, Reservation $reservation, RefuserAvecContrePropositionAction $action): JsonResponse
    {
        $this->authorize('respond', $reservation);

        $counterOffer = $action->executer($reservation, $request->user(), $request->validated());

        return CounterOfferResource::make($counterOffer)->response()->setStatusCode(201);
    }
}
