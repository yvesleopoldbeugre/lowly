<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Catalogue\Requests\StoreVehicleRequest;
use App\Domains\Catalogue\Requests\UpdateVehicleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (véhicules du partenaire connecté).
 */
class PartnerVehicleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (GET /api/v1/partner/vehicles).');
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (POST /api/v1/partner/vehicles).');
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (PATCH /api/v1/partner/vehicles/{id}).');
    }
}
