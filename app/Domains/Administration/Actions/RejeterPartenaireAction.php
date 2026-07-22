<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Administration\Exceptions\PartnerNotPendingException;
use App\Domains\Administration\Models\AdminAction;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`POST /admin/partners/{id}/reject`).
 */
final class RejeterPartenaireAction
{
    /**
     * @param  array{notes?: ?string}  $data
     */
    public function executer(Partner $partner, User $admin, array $data): Partner
    {
        if ($partner->status === 'rejete') {
            return $partner;
        }

        if ($partner->status !== 'en_attente') {
            throw new PartnerNotPendingException;
        }

        $partner->update(['status' => 'rejete']);

        AdminAction::create([
            'admin_id' => $admin->id,
            'action_type' => 'rejet_partenaire',
            'target_type' => 'partner',
            'target_id' => $partner->id,
            'notes' => $data['notes'] ?? null,
        ]);

        return $partner;
    }
}
