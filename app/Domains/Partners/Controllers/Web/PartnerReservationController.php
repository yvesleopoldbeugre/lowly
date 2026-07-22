<?php

namespace App\Domains\Partners\Controllers\Web;

use App\Domains\Catalogue\Actions\ListPartnerResidences;
use App\Domains\Catalogue\Actions\ListPartnerVehicles;
use App\Domains\Reservation\Actions\ListPartnerReservations;
use App\Domains\Reservation\Models\Reservation;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Reservation — demandes et réservations reçues par le partenaire
 * connecté, voir UX_UI.md §6.4.
 */
class PartnerReservationController extends Controller
{
    public function index(Request $request, ListPartnerReservations $action): View
    {
        $reservations = $action->executer($request->user()->partner, []);
        $reservations->getCollection()->load('reservable');

        return view('pages.partner.reservations.index', [
            'title' => 'Réservations',
            'reservations' => $reservations,
        ]);
    }

    public function show(
        Reservation $reservation,
        ListPartnerResidences $listResidences,
        ListPartnerVehicles $listVehicles,
    ): View {
        $this->authorize('view', $reservation);

        $partner = auth()->user()->partner;

        return view('pages.partner.reservations.show', [
            'title' => 'Réservation',
            'reservation' => $reservation->load(['reservable', 'client', 'counterOffer.proposedReservable']),
            'residences' => $listResidences->executer($partner, ['per_page' => 100])->getCollection(),
            'vehicles' => $listVehicles->executer($partner, ['per_page' => 100])->getCollection(),
        ]);
    }
}
