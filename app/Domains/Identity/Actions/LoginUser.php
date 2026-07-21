<?php

namespace App\Domains\Identity\Actions;

use App\Domains\Identity\Exceptions\InvalidCredentialsException;
use App\Domains\Identity\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Domaine Identity — voir API_GUIDE.md §9 (`POST /api/v1/auth/login`).
 */
final class LoginUser
{
    public function executer(string $email, string $password, bool $remember = false): User
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            throw new InvalidCredentialsException;
        }

        request()->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
