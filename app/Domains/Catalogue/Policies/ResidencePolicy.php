<?php

namespace App\Domains\Catalogue\Policies;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Identity\Models\User;

/**
 * Domaine Catalogue — voir docs/engineering/10-security-guidelines.md §4.
 */
final class ResidencePolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Residence $residence): bool
    {
        return $user->id === $residence->partner->user_id
            && in_array($residence->status, ['brouillon', 'rejetee', 'publiee'], true);
    }

    public function managePhotos(User $user, Residence $residence): bool
    {
        return $user->id === $residence->partner->user_id;
    }
}
