<?php

namespace App\Domains\Communication\Policies;

use App\Domains\Communication\Models\Notification;
use App\Domains\Identity\Models\User;

/**
 * Domaine Communication — voir docs/engineering/10-security-guidelines.md §4.
 */
final class NotificationPolicy
{
    public function view(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }
}
