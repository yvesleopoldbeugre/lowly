<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Reservation\Events\ContrePropositionExpiree;
use App\Domains\Reservation\Models\CounterOffer;
use Illuminate\Support\Facades\DB;

/**
 * Domaine Reservation — voir BUSINESS_RULES.md §6.2 et UML.md §5.4,
 * exécutée périodiquement par la commande Artisan
 * `reservations:expire-counter-offers`. Aucun utilisateur n'est à l'origine
 * de cette transition (`changed_by` nul dans l'historique).
 */
final class ExpirerContrePropositionsAction
{
    public function executer(): int
    {
        $counterOffers = CounterOffer::query()
            ->where('status', 'en_attente')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($counterOffers as $counterOffer) {
            DB::transaction(function () use ($counterOffer) {
                $counterOffer->update(['status' => 'expiree']);

                $reservation = $counterOffer->originalReservation;
                $reservation->update(['status' => 'expiree']);

                ContrePropositionExpiree::dispatch($reservation, $counterOffer);
            });
        }

        return $counterOffers->count();
    }
}
