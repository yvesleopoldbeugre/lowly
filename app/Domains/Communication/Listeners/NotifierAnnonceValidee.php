<?php

namespace App\Domains\Communication\Listeners;

use App\Domains\Administration\Events\AnnonceValidee;
use App\Domains\Communication\Models\Notification;

/**
 * Domaine Communication — voir ARCHITECTURE.md §9/§12.
 */
final class NotifierAnnonceValidee
{
    public function handle(AnnonceValidee $event): void
    {
        Notification::create([
            'user_id' => $event->listing->partner->user_id,
            'type' => 'annonce_validee',
            'payload' => [
                'listing_type' => $event->listing->getMorphClass(),
                'listing_id' => $event->listing->id,
            ],
        ]);
    }
}
