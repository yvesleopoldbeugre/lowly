<?php

namespace App\Domains\Availability\Controllers\Api;

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Availability\Requests\StoreAvailabilityBlockRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Domaine Availability — voir API_GUIDE.md §11, BUSINESS_RULES.md §4.2 et §7.
 */
class PartnerAvailabilityBlockController extends Controller
{
    public function store(StoreAvailabilityBlockRequest $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (POST /api/v1/partner/availability-blocks).');
    }

    public function destroy(AvailabilityBlock $availabilityBlock): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (DELETE /api/v1/partner/availability-blocks/{id}).');
    }
}
