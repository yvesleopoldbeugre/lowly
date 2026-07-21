<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Partners\Models\Partner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`GET /api/v1/partner/vehicles`).
 */
final class ListPartnerVehicles
{
    /**
     * @param  array{per_page?: ?int}  $filters
     */
    public function executer(Partner $partner, array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return Vehicle::query()
            ->where('partner_id', $partner->id)
            ->latest()
            ->paginate($perPage);
    }
}
