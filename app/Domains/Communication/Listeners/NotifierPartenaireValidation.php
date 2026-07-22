<?php

namespace App\Domains\Communication\Listeners;

use App\Domains\Administration\Events\PartenaireValide;
use App\Domains\Communication\Models\Notification;

/**
 * Domaine Communication — voir ARCHITECTURE.md §8.3/§9/§12.
 */
final class NotifierPartenaireValidation
{
    public function handle(PartenaireValide $event): void
    {
        Notification::create([
            'user_id' => $event->partner->user_id,
            'type' => 'partenaire_valide',
            'payload' => ['partner_id' => $event->partner->id],
        ]);
    }
}
