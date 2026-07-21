<?php

namespace App\Domains\Partners\Actions;

use App\Domains\Partners\Models\Partner;

/**
 * Domaine Partners — voir API_GUIDE.md §11 (`GET /api/v1/partner/dashboard`).
 *
 * Limité aux données du domaine Catalogue disponibles aujourd'hui. Les
 * statistiques de réservation (demandes en attente, séjours à venir) sont
 * différées à la tranche Reservation — voir ROADMAP.md §3.
 */
final class GetPartnerDashboard
{
    /**
     * @return array{residences: array<string, int>, vehicles: array<string, int>}
     */
    public function executer(Partner $partner): array
    {
        return [
            'residences' => $partner->residences()
                ->selectRaw('status, count(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
                ->all(),
            'vehicles' => $partner->vehicles()
                ->selectRaw('status, count(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
                ->all(),
        ];
    }
}
