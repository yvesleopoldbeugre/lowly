<?php

namespace App\Domains\Communication\Actions;

use App\Domains\Communication\Models\Notification;

/**
 * Domaine Communication — voir API_GUIDE.md §10
 * (`PATCH /api/v1/notifications/{id}/read`). Idempotent : rejouer l'action
 * sur une notification déjà lue renvoie l'état actuel sans erreur, voir
 * docs/engineering/09-api-guidelines.md §8.
 */
final class MarkNotificationRead
{
    public function executer(Notification $notification): Notification
    {
        if (! $notification->isRead()) {
            $notification->update(['read_at' => now()]);
        }

        return $notification;
    }
}
