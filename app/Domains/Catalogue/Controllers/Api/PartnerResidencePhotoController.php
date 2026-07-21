<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\ResidencePhoto;
use App\Domains\Catalogue\Requests\StoreResidencePhotoRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (gestion des photos, UX_UI.md §6.6).
 */
class PartnerResidencePhotoController extends Controller
{
    public function store(StoreResidencePhotoRequest $request, Residence $residence): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (POST /api/v1/partner/residences/{id}/photos).');
    }

    public function destroy(Residence $residence, ResidencePhoto $photo): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (DELETE /api/v1/partner/residences/{id}/photos/{photoId}).');
    }
}
