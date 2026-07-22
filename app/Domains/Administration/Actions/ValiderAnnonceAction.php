<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Administration\Events\AnnonceValidee;
use App\Domains\Administration\Exceptions\ListingNotPendingException;
use App\Domains\Administration\Models\AdminAction;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Domaine Administration — voir API_GUIDE.md §12
 * (`POST /admin/listings/{type}/{id}/validate`). `Residence` et `Vehicle`
 * partagent le même cycle de validation mais des valeurs d'énumération
 * différentes (`publiee` vs `publie`) — voir DATABASE.md §6.1/§6.3.
 */
final class ValiderAnnonceAction
{
    public function executer(string $type, string $id, User $admin): Residence|Vehicle
    {
        $listing = $this->resoudreAnnonce($type, $id);
        $publishedStatus = $type === 'vehicle' ? 'publie' : 'publiee';

        if ($listing->status === $publishedStatus) {
            return $listing;
        }

        if ($listing->status !== 'en_validation') {
            throw new ListingNotPendingException;
        }

        $listing->update(['status' => $publishedStatus]);

        AdminAction::create([
            'admin_id' => $admin->id,
            'action_type' => 'validation_annonce',
            'target_type' => $listing->getMorphClass(),
            'target_id' => $listing->id,
        ]);

        AnnonceValidee::dispatch($listing);

        return $listing;
    }

    private function resoudreAnnonce(string $type, string $id): Residence|Vehicle
    {
        $listing = match ($type) {
            'residence' => Residence::find($id),
            'vehicle' => Vehicle::find($id),
            default => null,
        };

        if (! $listing) {
            throw new ModelNotFoundException;
        }

        return $listing;
    }
}
