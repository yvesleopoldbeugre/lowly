<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Domaine Reservation — voir API_GUIDE.md §11 (`GET /partner/reservations`),
 * UX_UI.md §6.4 (demandes et réservations reçues par le partenaire connecté).
 */
final class ListPartnerReservations
{
    /**
     * @param  array{per_page?: ?int}  $filters
     */
    public function executer(Partner $partner, array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $residenceIds = $partner->residences()->pluck('id');
        $vehicleIds = $partner->vehicles()->pluck('id');

        return Reservation::query()
            ->where(function (Builder $query) use ($residenceIds, $vehicleIds) {
                $query
                    ->where(fn (Builder $q) => $q->where('reservable_type', 'residence')->whereIn('reservable_id', $residenceIds))
                    ->orWhere(fn (Builder $q) => $q->where('reservable_type', 'vehicle')->whereIn('reservable_id', $vehicleIds));
            })
            ->latest()
            ->paginate($perPage);
    }
}
