<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Administration\Models\PlatformSetting;
use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Events\ContrePropositionSoumise;
use App\Domains\Reservation\Exceptions\ReservationInvalidPeriodException;
use App\Domains\Reservation\Exceptions\ReservationPeriodUnavailableException;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

/**
 * Domaine Reservation — voir API_GUIDE.md §11
 * (`POST /partner/reservations/{id}/counter-offer`), BUSINESS_RULES.md §6.1/§6.2
 * et UML.md §5.3. Le bien alternatif doit appartenir au même partenaire et
 * être disponible sur la période proposée ("vérification automatique du
 * calendrier avant soumission").
 */
final class RefuserAvecContrePropositionAction
{
    /**
     * @param  array{proposed_reservable_type: string, proposed_reservable_id: string, start_date?: string, end_date?: string}  $data
     */
    public function executer(Reservation $reservation, User $partner, array $data): CounterOffer
    {
        $proposedReservable = $this->resoudreReservableDuPartenaire($data['proposed_reservable_type'], $data['proposed_reservable_id'], $partner);

        $start = isset($data['start_date']) ? Carbon::parse($data['start_date'])->startOfDay() : $reservation->period['start'];
        $end = isset($data['end_date']) ? Carbon::parse($data['end_date'])->startOfDay() : $reservation->period['end'];

        if ($start->diffInDays($end) < 1) {
            throw new ReservationInvalidPeriodException;
        }

        if (AvailabilityBlock::overlapping($proposedReservable->getMorphClass(), $proposedReservable->id, ['start' => $start, 'end' => $end])->exists()) {
            throw new ReservationPeriodUnavailableException;
        }

        $reservation->update(['status' => 'contre_proposee']);

        $counterOffer = CounterOffer::create([
            'original_reservation_id' => $reservation->id,
            'proposed_reservable_type' => $proposedReservable->getMorphClass(),
            'proposed_reservable_id' => $proposedReservable->id,
            'proposed_period' => ['start' => $start, 'end' => $end],
            'status' => 'en_attente',
            'expires_at' => now()->addHours(PlatformSetting::hours('counter_offer_response_delay_hours', 72)),
        ]);

        ContrePropositionSoumise::dispatch($reservation->refresh(), $counterOffer, $partner);

        return $counterOffer;
    }

    private function resoudreReservableDuPartenaire(string $type, string $id, User $partner): Residence|Vehicle
    {
        $reservable = $type === 'vehicle' ? Vehicle::find($id) : Residence::find($id);

        if (! $reservable || ! $reservable->isPublished() || $reservable->partner->user_id !== $partner->id) {
            throw new ModelNotFoundException;
        }

        return $reservable;
    }
}
