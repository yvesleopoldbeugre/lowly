<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Administration\Actions\ListUsers;
use App\Domains\Administration\Actions\SuspendUserAction;
use App\Domains\Identity\Models\User;
use App\Domains\Identity\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Administration — voir API_GUIDE.md §12, UX_UI.md §7.3.
 */
class AdminUserController extends Controller
{
    public function index(Request $request, ListUsers $action): AnonymousResourceCollection
    {
        return UserResource::collection($action->executer([
            'role' => $request->string('role')->value() ?: null,
            'status' => $request->string('status')->value() ?: null,
            'per_page' => $request->integer('per_page'),
        ]));
    }

    public function suspend(User $user, Request $request, SuspendUserAction $action): UserResource
    {
        return UserResource::make($action->executer($user, $request->user()));
    }
}
