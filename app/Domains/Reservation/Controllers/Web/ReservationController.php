<?php

namespace App\Domains\Reservation\Controllers\Web;

use App\Domains\Reservation\Actions\ListClientReservations;
use App\Domains\Reservation\Models\Reservation;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Reservation — historique et suivi du client connecté, voir
 * UX_UI.md §5.2/§5.3 et docs/ux/mockups/04-reservation-client.html.
 */
class ReservationController extends Controller
{
    public function index(Request $request, ListClientReservations $action): View
    {
        $reservations = $action->executer($request->user(), []);
        $reservations->getCollection()->load('reservable');

        return view('pages.client.reservations.index', [
            'title' => 'Mes réservations',
            'reservations' => $reservations,
        ]);
    }

    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);

        return view('pages.client.reservations.show', [
            'title' => 'Réservation',
            'reservation' => $reservation->load(['reservable', 'counterOffer.proposedReservable', 'statusHistory']),
        ]);
    }
}
