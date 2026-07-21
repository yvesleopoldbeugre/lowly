<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9 (`GET /api/v1/vehicles`) et
 * docs/engineering/09-api-guidelines.md §5 (pagination).
 */
final class ListPublishedVehicles
{
    /**
     * @param  array{min_price?: ?float, max_price?: ?float, per_page?: ?int}  $filters
     */
    public function executer(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return Vehicle::query()
            ->published()
            ->priceBetween($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->paginate($perPage);
    }
}
