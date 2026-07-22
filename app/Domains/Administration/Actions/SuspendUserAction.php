<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Administration\Models\AdminAction;
use App\Domains\Identity\Models\User;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`PATCH /admin/users/{id}/suspend`).
 * Idempotent : suspendre un utilisateur déjà suspendu ne modifie pas
 * `suspended_at` et n'écrit pas de nouvelle ligne d'audit.
 */
final class SuspendUserAction
{
    public function executer(User $user, User $admin): User
    {
        if ($user->isSuspended()) {
            return $user;
        }

        $user->update(['suspended_at' => now()]);

        AdminAction::create([
            'admin_id' => $admin->id,
            'action_type' => 'suspension_utilisateur',
            'target_type' => 'user',
            'target_id' => $user->id,
        ]);

        return $user;
    }
}
