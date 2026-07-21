<?php

namespace App\Domains\Identity\Controllers\Api;

use App\Domains\Identity\Requests\UpdateProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Identity — voir API_GUIDE.md §10 (`/api/v1/me`).
 */
class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (GET /api/v1/me).');
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (PATCH /api/v1/me).');
    }
}
