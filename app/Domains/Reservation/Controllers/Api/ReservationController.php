<?php

namespace App\Domains\Reservation\Controllers\Api;

use App\Domains\Reservation\Actions\CreerDemandeReservationAction;
use App\Domains\Reservation\Actions\ListClientReservations;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Requests\StoreReservationRequest;
use App\Domains\Reservation\Resources\ReservationResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Reservation — voir API_GUIDE.md §10, BUSINESS_RULES.md §5.
 */
class ReservationController extends Controller
{
    public function store(StoreReservationRequest $request, CreerDemandeReservationAction $action): JsonResponse
    {
        $reservation = $action->executer($request->user(), $request->validated());

        return ReservationResource::make($reservation)->response()->setStatusCode(201);
    }

    public function index(Request $request, ListClientReservations $action): AnonymousResourceCollection
    {
        return ReservationResource::collection(
            $action->executer($request->user(), ['per_page' => $request->integer('per_page')])
        );
    }

    public function show(Reservation $reservation): ReservationResource
    {
        $this->authorize('view', $reservation);

        return ReservationResource::make($reservation->load('counterOffer'));
    }
}
