<?php

namespace App\Domains\Reservation\Listeners;

use App\Domains\Reservation\Events\ContrePropositionExpiree;
use App\Domains\Reservation\Events\ContrePropositionSoumise;
use App\Domains\Reservation\Events\ReservationConfirmee;
use App\Domains\Reservation\Events\ReservationRefusee;
use App\Domains\Reservation\Models\ReservationStatusHistory;

/**
 * Domaine Reservation — voir ARCHITECTURE.md §8.2 (listener `L3` de
 * UML.md §5.2), écrit une ligne `ReservationStatusHistory` à chaque
 * transition d'état. Enregistré explicitement pour chaque événement dans
 * AppServiceProvider::boot() (une classe, plusieurs méthodes — pas
 * d'auto-discovery, cohérent avec la structure par domaine).
 */
final class EnregistrerHistorique
{
    public function confirmee(ReservationConfirmee $event): void
    {
        $this->ecrire($event->reservation->id, $event->previousStatus, 'confirmee', $event->changedBy?->id);
    }

    public function refusee(ReservationRefusee $event): void
    {
        $this->ecrire($event->reservation->id, $event->previousStatus, 'refusee', $event->changedBy?->id);
    }

    public function contrePropositionSoumise(ContrePropositionSoumise $event): void
    {
        $this->ecrire($event->reservation->id, 'en_attente', 'contre_proposee', $event->changedBy->id);
    }

    public function contrePropositionExpiree(ContrePropositionExpiree $event): void
    {
        $this->ecrire($event->reservation->id, 'contre_proposee', 'expiree', null);
    }

    private function ecrire(string $reservationId, string $previousStatus, string $newStatus, ?string $changedBy): void
    {
        ReservationStatusHistory::create([
            'reservation_id' => $reservationId,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'changed_at' => now(),
        ]);
    }
}
