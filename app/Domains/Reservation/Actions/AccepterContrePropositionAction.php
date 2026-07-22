<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Reservation\Events\ReservationConfirmee;
use App\Domains\Reservation\Exceptions\CounterOfferAlreadyAnsweredException;
use App\Domains\Reservation\Exceptions\CounterOfferExpiredException;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Support\Facades\DB;

/**
 * Domaine Reservation — voir API_GUIDE.md §10
 * (`.../counter-offers/{offerId}/accept`), BUSINESS_RULES.md §6.2 et
 * UML.md §5.3 : crée une nouvelle réservation confirmée rattachée à la
 * demande initiale, et déclenche le même cycle de blocage/notification
 * qu'une acceptation directe (`ReservationConfirmee`), dans la même
 * transaction — voir ConfirmerReservationAction.
 */
final class AccepterContrePropositionAction
{
    public function executer(CounterOffer $counterOffer): Reservation
    {
        if ($counterOffer->isExpired()) {
            throw new CounterOfferExpiredException;
        }

        if ($counterOffer->status !== 'en_attente') {
            throw new CounterOfferAlreadyAnsweredException;
        }

        return DB::transaction(function () use ($counterOffer) {
            $original = $counterOffer->originalReservation;
            $proposedReservable = $counterOffer->proposedReservable;
            $nightsCount = $counterOffer->proposed_period['start']->diffInDays($counterOffer->proposed_period['end']);

            $counterOffer->update(['status' => 'acceptee']);

            $nouvelleReservation = Reservation::create([
                'client_id' => $original->client_id,
                'reservable_type' => $counterOffer->proposed_reservable_type,
                'reservable_id' => $counterOffer->proposed_reservable_id,
                'period' => $counterOffer->proposed_period,
                'nights_count' => $nightsCount,
                'total_amount' => round((float) $proposedReservable->dailyRate() * $nightsCount, 2),
                'status' => 'confirmee',
                'parent_reservation_id' => $original->id,
            ]);

            ReservationConfirmee::dispatch($nouvelleReservation, 'en_attente', $original->client);

            return $nouvelleReservation;
        });
    }
}
