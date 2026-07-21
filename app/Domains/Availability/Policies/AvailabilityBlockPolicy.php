<?php

namespace App\Domains\Availability\Policies;

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;

/**
 * Domaine Availability — voir docs/engineering/10-security-guidelines.md §4.
 */
final class AvailabilityBlockPolicy
{
    public function create(User $user, Residence|Vehicle $blockable): bool
    {
        return $user->id === $blockable->partner->user_id;
    }

    public function delete(User $user, AvailabilityBlock $availabilityBlock): bool
    {
        return $user->id === $availabilityBlock->blockable->partner->user_id
            && $availabilityBlock->origin !== 'reservation';
    }
}
