<?php

namespace App\Domains\Reservation\Actions;

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Events\DemandeReservationCreee;
use App\Domains\Reservation\Exceptions\ReservationInvalidPeriodException;
use App\Domains\Reservation\Exceptions\ReservationPeriodUnavailableException;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

/**
 * Domaine Reservation — voir API_GUIDE.md §10 (`POST /reservations`),
 * BUSINESS_RULES.md §5.1 et UML.md §5.1. Aucun blocage calendrier n'est
 * créé à cette étape.
 */
final class CreerDemandeReservationAction
{
    /**
     * @param  array{reservable_type: string, reservable_id: string, start_date: string, end_date: string}  $data
     */
    public function executer(User $client, array $data): Reservation
    {
        $reservable = $this->resoudreReservable($data['reservable_type'], $data['reservable_id']);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = Carbon::parse($data['end_date'])->startOfDay();
        $nightsCount = $start->diffInDays($end);

        if ($nightsCount < 1) {
            throw new ReservationInvalidPeriodException;
        }

        if (AvailabilityBlock::overlapping($reservable->getMorphClass(), $reservable->id, ['start' => $start, 'end' => $end])->exists()) {
            throw new ReservationPeriodUnavailableException;
        }

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'reservable_type' => $reservable->getMorphClass(),
            'reservable_id' => $reservable->id,
            'period' => ['start' => $start, 'end' => $end],
            'nights_count' => $nightsCount,
            'total_amount' => round((float) $reservable->dailyRate() * $nightsCount, 2),
            'status' => 'en_attente',
        ]);

        DemandeReservationCreee::dispatch($reservation);

        return $reservation;
    }

    private function resoudreReservable(string $type, string $id): Residence|Vehicle
    {
        $reservable = $type === 'vehicle' ? Vehicle::find($id) : Residence::find($id);

        if (! $reservable || ! $reservable->isPublished()) {
            throw new ModelNotFoundException;
        }

        return $reservable;
    }
}
