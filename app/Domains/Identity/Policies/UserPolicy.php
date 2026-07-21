<?php

namespace App\Domains\Identity\Policies;

use App\Domains\Identity\Models\User;

/**
 * Domaine Identity — voir docs/engineering/10-security-guidelines.md §4.
 */
final class UserPolicy
{
    public function update(User $authUser, User $target): bool
    {
        return $authUser->id === $target->id;
    }
}
