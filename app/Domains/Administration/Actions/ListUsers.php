<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Identity\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`GET /admin/users`),
 * UX_UI.md §7.3 (recherche + filtres rôle/statut).
 */
final class ListUsers
{
    /**
     * @param  array{role?: ?string, status?: ?string, per_page?: ?int}  $filters
     */
    public function executer(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return User::query()
            ->when(
                in_array($filters['role'] ?? null, ['client', 'partner', 'admin'], true),
                fn ($query) => $query->where('role', $filters['role']),
            )
            ->when(($filters['status'] ?? null) === 'suspended', fn ($query) => $query->whereNotNull('suspended_at'))
            ->when(($filters['status'] ?? null) === 'active', fn ($query) => $query->whereNull('suspended_at'))
            ->latest()
            ->paginate($perPage);
    }
}
