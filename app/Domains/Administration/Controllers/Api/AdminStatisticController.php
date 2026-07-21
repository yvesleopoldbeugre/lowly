<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Administration — voir API_GUIDE.md §12, PRODUCT.md §11
 * (indicateurs de succès), UX_UI.md §7.4.
 */
class AdminStatisticController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (GET /api/v1/admin/statistics).');
    }
}
