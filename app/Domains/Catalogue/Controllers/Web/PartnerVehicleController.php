<?php

namespace App\Domains\Catalogue\Controllers\Web;

use App\Domains\Catalogue\Actions\ListPartnerVehicles;
use App\Domains\Catalogue\Models\Vehicle;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Catalogue — gestion des véhicules du partenaire connecté, voir
 * UX_UI.md §6.2.
 */
class PartnerVehicleController extends Controller
{
    public function index(Request $request, ListPartnerVehicles $action): View
    {
        return view('pages.partner.vehicles.index', [
            'title' => 'Mes véhicules',
            'vehicles' => $action->executer($request->user()->partner, []),
        ]);
    }

    public function create(): View
    {
        return view('pages.partner.vehicles.form', [
            'title' => 'Nouveau véhicule',
            'vehicle' => null,
        ]);
    }

    public function edit(Vehicle $vehicle): View
    {
        $this->authorize('update', $vehicle);

        return view('pages.partner.vehicles.form', [
            'title' => 'Modifier '.$vehicle->brand.' '.$vehicle->model,
            'vehicle' => $vehicle->load('photos'),
        ]);
    }
}
