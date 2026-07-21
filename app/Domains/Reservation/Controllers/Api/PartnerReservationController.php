<?php

namespace App\Domains\Reservation\Controllers\Api;

use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Requests\StoreCounterOfferRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Reservation — voir API_GUIDE.md §11, BUSINESS_RULES.md §5.2
 * (traitement d'une demande par le partenaire).
 */
class PartnerReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (GET /api/v1/partner/reservations).');
    }

    public function accept(Reservation $reservation): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (POST /api/v1/partner/reservations/{id}/accept).');
    }

    public function reject(Reservation $reservation): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (POST /api/v1/partner/reservations/{id}/reject).');
    }

    public function counterOffer(StoreCounterOfferRequest $request, Reservation $reservation): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (POST /api/v1/partner/reservations/{id}/counter-offer).');
    }
}
