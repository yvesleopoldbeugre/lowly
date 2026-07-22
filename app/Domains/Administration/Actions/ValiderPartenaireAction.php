<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Administration\Events\PartenaireValide;
use App\Domains\Administration\Exceptions\PartnerNotPendingException;
use App\Domains\Administration\Models\AdminAction;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;

/**
 * Domaine Administration — voir API_GUIDE.md §12
 * (`POST /admin/partners/{id}/validate`), ARCHITECTURE.md §8.3.
 */
final class ValiderPartenaireAction
{
    public function executer(Partner $partner, User $admin): Partner
    {
        if ($partner->status === 'valide') {
            return $partner;
        }

        if ($partner->status !== 'en_attente') {
            throw new PartnerNotPendingException;
        }

        $partner->update([
            'status' => 'valide',
            'validated_at' => now(),
            'validated_by' => $admin->id,
        ]);

        AdminAction::create([
            'admin_id' => $admin->id,
            'action_type' => 'validation_partenaire',
            'target_type' => 'partner',
            'target_id' => $partner->id,
        ]);

        PartenaireValide::dispatch($partner);

        return $partner;
    }
}
