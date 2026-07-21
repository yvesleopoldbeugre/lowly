<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9.
 *
 * Recherche transverse résidences + véhicules. Paramètres documentés :
 * `type`, `city`, `start_date`, `end_date`, `min_price`, `max_price`,
 * `capacity` — voir API_GUIDE.md §9, note sous le tableau des endpoints publics.
 */
class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §9 (GET /api/v1/search).');
    }
}
