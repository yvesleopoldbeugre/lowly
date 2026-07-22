<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Models\ReservationStatusHistory;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`GET /admin/statistics`),
 * PRODUCT.md §11. Calcule 5 des 6 indicateurs listés — le "taux de
 * conversion recherche→demande" est omis, aucun suivi des événements de
 * recherche n'existant dans le domaine Catalogue (décision actée, voir
 * le plan de cette tranche).
 */
final class GetPlatformStatistics
{
    /**
     * @return array<string, mixed>
     */
    public function executer(): array
    {
        return [
            'reservation_acceptance_rate' => $this->reservationAcceptanceRate(),
            'average_partner_response_delay_hours' => $this->averagePartnerResponseDelayHours(),
            'counter_offer_acceptance_rate' => $this->counterOfferAcceptanceRate(),
            'active_validated_partners' => Partner::query()->where('status', 'valide')->count(),
            'confirmed_reservations_calendar_accuracy_rate' => $this->confirmedReservationsCalendarAccuracyRate(),
        ];
    }

    private function reservationAcceptanceRate(): ?float
    {
        $resolved = Reservation::query()->whereIn('status', ['confirmee', 'refusee', 'expiree'])->count();

        if ($resolved === 0) {
            return null;
        }

        $confirmed = Reservation::query()->where('status', 'confirmee')->count();

        return round($confirmed / $resolved, 3);
    }

    private function averagePartnerResponseDelayHours(): ?float
    {
        $delaysInMinutes = ReservationStatusHistory::query()
            ->where('previous_status', 'en_attente')
            ->with('reservation')
            ->get()
            ->map(fn (ReservationStatusHistory $history) => $history->reservation->created_at->diffInMinutes($history->changed_at));

        if ($delaysInMinutes->isEmpty()) {
            return null;
        }

        return round($delaysInMinutes->avg() / 60, 1);
    }

    private function counterOfferAcceptanceRate(): ?float
    {
        $resolved = CounterOffer::query()->whereIn('status', ['acceptee', 'refusee', 'expiree'])->count();

        if ($resolved === 0) {
            return null;
        }

        $accepted = CounterOffer::query()->where('status', 'acceptee')->count();

        return round($accepted / $resolved, 3);
    }

    private function confirmedReservationsCalendarAccuracyRate(): ?float
    {
        $confirmedIds = Reservation::query()->where('status', 'confirmee')->pluck('id');

        if ($confirmedIds->isEmpty()) {
            return null;
        }

        $blocked = AvailabilityBlock::query()
            ->where('origin', 'reservation')
            ->whereIn('reservation_id', $confirmedIds)
            ->count();

        return round($blocked / $confirmedIds->count(), 3);
    }
}
