<?php

namespace App\Domains\Identity\Controllers\Api;

use App\Domains\Identity\Actions\LoginUser;
use App\Domains\Identity\Actions\LogoutUser;
use App\Domains\Identity\Actions\RegisterUser;
use App\Domains\Identity\Requests\LoginRequest;
use App\Domains\Identity\Requests\RegisterRequest;
use App\Domains\Identity\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Domaine Identity — voir API_GUIDE.md §9.
 */
class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUser $action): JsonResponse
    {
        $user = $action->executer($request->validated());

        return UserResource::make($user)->response()->setStatusCode(201);
    }

    public function login(LoginRequest $request, LoginUser $action): JsonResponse
    {
        $user = $action->executer(
            $request->validated('email'),
            $request->validated('password'),
            $request->boolean('remember'),
        );

        return UserResource::make($user)->response();
    }

    public function logout(LogoutUser $action): Response
    {
        $action->executer();

        return response()->noContent();
    }
}
