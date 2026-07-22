<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Domaine Reservation — voir API_GUIDE.md §10 (`GET /reservations`),
 * BUSINESS_RULES.md §5.3/UX_UI.md §5.3 (historique du client connecté).
 */
final class ListClientReservations
{
    /**
     * @param  array{per_page?: ?int}  $filters
     */
    public function executer(User $client, array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return Reservation::query()
            ->where('client_id', $client->id)
            ->latest()
            ->paginate($perPage);
    }
}
