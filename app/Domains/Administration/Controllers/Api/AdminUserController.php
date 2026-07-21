<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Identity\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Administration — voir API_GUIDE.md §12, UX_UI.md §7.3.
 */
class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (GET /api/v1/admin/users).');
    }

    public function suspend(User $user): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §12 (PATCH /api/v1/admin/users/{id}/suspend).');
    }
}
