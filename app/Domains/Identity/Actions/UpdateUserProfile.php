<?php

namespace App\Domains\Identity\Actions;

use App\Domains\Identity\Models\User;

/**
 * Domaine Identity — voir API_GUIDE.md §10 (`PATCH /api/v1/me`).
 */
final class UpdateUserProfile
{
    /**
     * @param  array{full_name?: string, phone?: ?string, email?: string}  $data
     */
    public function executer(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user;
    }
}
