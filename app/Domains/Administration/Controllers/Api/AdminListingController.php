<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Administration\Requests\RejectListingRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Administration — voir API_GUIDE.md §12, UX_UI.md §7.2.
 *
 * `{type}` vaut `residence` ou `vehicle` (relation polymorphe — voir
 * DATABASE.md §6 et ARCHITECTURE.md §13). Résolution du modèle concret
 * différée à la phase Développement (pas de binding implicite possible
 * sur un couple {type}/{id} générique).
 */
class AdminListingController extends Controller
{
    public function pending(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (GET /api/v1/admin/listings/pending).');
    }

    public function validateListing(string $type, string $id): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (POST /api/v1/admin/listings/{type}/{id}/validate).');
    }

    public function reject(RejectListingRequest $request, string $type, string $id): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (POST /api/v1/admin/listings/{type}/{id}/reject).');
    }
}
