<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\Residence;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9 (`GET /api/v1/residences`) et
 * docs/engineering/09-api-guidelines.md §5 (pagination).
 */
final class ListPublishedResidences
{
    /**
     * @param  array{city?: ?string, min_price?: ?float, max_price?: ?float, capacity?: ?int, per_page?: ?int}  $filters
     */
    public function executer(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return Residence::query()
            ->published()
            ->inCity($filters['city'] ?? null)
            ->priceBetween($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->withCapacity($filters['capacity'] ?? null)
            ->paginate($perPage);
    }
}
