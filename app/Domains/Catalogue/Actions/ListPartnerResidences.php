<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Partners\Models\Partner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`GET /api/v1/partner/residences`).
 *
 * Contrairement à ListPublishedResidences (domaine public), retourne les
 * résidences du partenaire quel que soit leur statut.
 */
final class ListPartnerResidences
{
    /**
     * @param  array{per_page?: ?int}  $filters
     */
    public function executer(Partner $partner, array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return Residence::query()
            ->where('partner_id', $partner->id)
            ->latest()
            ->paginate($perPage);
    }
}
