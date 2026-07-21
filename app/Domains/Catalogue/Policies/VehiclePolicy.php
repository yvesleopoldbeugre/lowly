<?php

namespace App\Domains\Catalogue\Policies;

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;

/**
 * Domaine Catalogue — voir docs/engineering/10-security-guidelines.md §4.
 */
final class VehiclePolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->id === $vehicle->partner->user_id
            && in_array($vehicle->status, ['brouillon', 'rejete', 'publie'], true);
    }

    public function managePhotos(User $user, Vehicle $vehicle): bool
    {
        return $user->id === $vehicle->partner->user_id;
    }
}
