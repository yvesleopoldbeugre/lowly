<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Administration\Requests\UpdateSettingRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Administration — voir API_GUIDE.md §12, DATABASE.md §10.2, UX_UI.md §7.5.
 */
class AdminSettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (GET /api/v1/admin/settings).');
    }

    public function update(UpdateSettingRequest $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (PATCH /api/v1/admin/settings).');
    }
}
