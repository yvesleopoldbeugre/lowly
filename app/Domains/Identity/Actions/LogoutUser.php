<?php

namespace App\Domains\Identity\Actions;

use Illuminate\Support\Facades\Auth;

/**
 * Domaine Identity — voir API_GUIDE.md §9 (`POST /api/v1/auth/logout`).
 */
final class LogoutUser
{
    public function executer(): void
    {
        Auth::logout();

        $session = request()->session();
        $session->invalidate();
        $session->regenerateToken();
    }
}
