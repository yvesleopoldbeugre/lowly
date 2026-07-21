<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Models\Residence;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9 (endpoints publics).
 */
class ResidenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §9 (GET /api/v1/residences).');
    }

    public function show(Residence $residence): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §9 (GET /api/v1/residences/{id}).');
    }
}
