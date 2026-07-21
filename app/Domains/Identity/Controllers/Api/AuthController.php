<?php

namespace App\Domains\Identity\Controllers\Api;

use App\Domains\Identity\Requests\LoginRequest;
use App\Domains\Identity\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Domaine Identity — voir API_GUIDE.md §9.
 *
 * Squelette de conception : la logique métier (création du compte, du
 * profil Partner en attente le cas échéant, authentification de session)
 * est implémentée en phase Développement via une Action du domaine Identity
 * — voir ENGINEERING.md §5.
 */
class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §9 (POST /api/v1/auth/register).');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §9 (POST /api/v1/auth/login).');
    }
}
