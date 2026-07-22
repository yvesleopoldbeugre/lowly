<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Partners\Models\Partner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`GET /admin/partners/pending`).
 */
final class ListPendingPartners
{
    /**
     * @param  array{per_page?: ?int}  $filters
     */
    public function executer(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return Partner::query()
            ->where('status', 'en_attente')
            ->latest()
            ->paginate($perPage);
    }
}
