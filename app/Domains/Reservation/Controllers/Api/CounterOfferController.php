<?php

namespace App\Domains\Reservation\Controllers\Api;

use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Domaine Reservation — voir API_GUIDE.md §10, BUSINESS_RULES.md §6
 * (réponse du client à une contre-proposition).
 */
class CounterOfferController extends Controller
{
    public function accept(Reservation $reservation, CounterOffer $counterOffer): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (.../counter-offers/{offerId}/accept).');
    }

    public function reject(Reservation $reservation, CounterOffer $counterOffer): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (.../counter-offers/{offerId}/reject).');
    }
}
