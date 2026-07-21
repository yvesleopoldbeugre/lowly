<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Requests\StoreResidenceRequest;
use App\Domains\Catalogue\Requests\UpdateResidenceRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (résidences du partenaire connecté).
 */
class PartnerResidenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (GET /api/v1/partner/residences).');
    }

    public function store(StoreResidenceRequest $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (POST /api/v1/partner/residences).');
    }

    public function update(UpdateResidenceRequest $request, Residence $residence): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (PATCH /api/v1/partner/residences/{id}).');
    }
}
