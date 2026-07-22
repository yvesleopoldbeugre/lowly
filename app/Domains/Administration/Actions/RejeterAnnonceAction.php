<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Administration\Exceptions\ListingNotPendingException;
use App\Domains\Administration\Models\AdminAction;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Domaine Administration — voir API_GUIDE.md §12
 * (`POST /admin/listings/{type}/{id}/reject`). Le motif est obligatoire
 * (`RejectListingRequest`, cohérent avec UX_UI.md §7.2 "motif obligatoire").
 */
final class RejeterAnnonceAction
{
    /**
     * @param  array{reason: string}  $data
     */
    public function executer(string $type, string $id, User $admin, array $data): Residence|Vehicle
    {
        $listing = $this->resoudreAnnonce($type, $id);
        $rejectedStatus = $type === 'vehicle' ? 'rejete' : 'rejetee';

        if ($listing->status === $rejectedStatus) {
            return $listing;
        }

        if ($listing->status !== 'en_validation') {
            throw new ListingNotPendingException;
        }

        $listing->update(['status' => $rejectedStatus]);

        AdminAction::create([
            'admin_id' => $admin->id,
            'action_type' => 'rejet_annonce',
            'target_type' => $listing->getMorphClass(),
            'target_id' => $listing->id,
            'notes' => $data['reason'],
        ]);

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
