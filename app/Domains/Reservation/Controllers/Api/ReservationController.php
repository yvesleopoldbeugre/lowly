<?php

namespace App\Domains\Reservation\Controllers\Api;

use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Requests\StoreReservationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Reservation — voir API_GUIDE.md §10, BUSINESS_RULES.md §5.
 */
class ReservationController extends Controller
{
    public function store(StoreReservationRequest $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (POST /api/v1/reservations).');
    }

    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (GET /api/v1/reservations).');
    }

    public function show(Reservation $reservation): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (GET /api/v1/reservations/{id}).');
    }
}
