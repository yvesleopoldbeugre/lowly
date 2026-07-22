<?php

namespace App\Domains\Communication\Actions;

use App\Domains\Identity\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Domaine Communication — voir API_GUIDE.md §10 (`GET /api/v1/notifications`).
 */
final class ListUserNotifications
{
    /**
     * @param  array{per_page?: ?int}  $filters
     */
    public function executer(User $user, array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return $user->notifications()->latest()->paginate($perPage);
    }
}
