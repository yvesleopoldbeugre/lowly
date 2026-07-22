<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Administration\Actions\ListPendingListings;
use App\Domains\Administration\Actions\RejeterAnnonceAction;
use App\Domains\Administration\Actions\ValiderAnnonceAction;
use App\Domains\Administration\Requests\RejectListingRequest;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Resources\ResidenceResource;
use App\Domains\Catalogue\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Administration — voir API_GUIDE.md §12, UX_UI.md §7.2.
 *
 * `{type}` vaut `residence` ou `vehicle` (relation polymorphe — voir
 * DATABASE.md §6 et ARCHITECTURE.md §13).
 */
class AdminListingController extends Controller
{
    public function pending(Request $request, ListPendingListings $action): JsonResponse
    {
        $listings = $action->executer()->map(
            fn ($listing) => $listing instanceof Residence ? ResidenceResource::make($listing) : VehicleResource::make($listing)
        );

        return response()->json(['data' => $listings]);
    }

    public function validateListing(string $type, string $id, Request $request, ValiderAnnonceAction $action): JsonResource
    {
        $listing = $action->executer($type, $id, $request->user());

        return $type === 'vehicle' ? VehicleResource::make($listing) : ResidenceResource::make($listing);
    }

    public function reject(RejectListingRequest $request, string $type, string $id, RejeterAnnonceAction $action): JsonResource
    {
        $listing = $action->executer($type, $id, $request->user(), $request->validated());

        return $type === 'vehicle' ? VehicleResource::make($listing) : ResidenceResource::make($listing);
    }
}
