<?php

namespace App\Domains\Identity\Controllers\Api;

use App\Domains\Identity\Actions\UpdateUserProfile;
use App\Domains\Identity\Requests\UpdateProfileRequest;
use App\Domains\Identity\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Identity — voir API_GUIDE.md §9 (`/api/v1/me`, accessible à tout
 * rôle authentifié — voir routes/api.php).
 */
class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return UserResource::make($request->user())->response();
    }

    public function update(UpdateProfileRequest $request, UpdateUserProfile $action): JsonResponse
    {
        $this->authorize('update', $request->user());

        $user = $action->executer($request->user(), $request->validated());

        return UserResource::make($user)->response();
    }
}
