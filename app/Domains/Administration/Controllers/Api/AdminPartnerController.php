<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Administration\Requests\RejectPartnerRequest;
use App\Domains\Partners\Models\Partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Administration — voir API_GUIDE.md §12, UX_UI.md §7.1.
 */
class AdminPartnerController extends Controller
{
    public function pending(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (GET /api/v1/admin/partners/pending).');
    }

    public function validatePartner(Partner $partner): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (POST /api/v1/admin/partners/{id}/validate).');
    }

    public function reject(RejectPartnerRequest $request, Partner $partner): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (POST /api/v1/admin/partners/{id}/reject).');
    }
}
