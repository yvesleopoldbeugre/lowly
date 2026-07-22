<?php

namespace App\Domains\Catalogue\Controllers\Web;

use App\Domains\Catalogue\Models\Vehicle;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Domaine Catalogue — détail d'un véhicule, voir UX_UI.md §4.3 et
 * docs/ux/mockups/02-detail-annonce.html.
 */
class VehicleController extends Controller
{
    public function show(Vehicle $vehicle): View
    {
        abort_unless($vehicle->isPublished(), 404);

        return view('pages.public.listing-detail', [
            'title' => "{$vehicle->brand} {$vehicle->model}",
            'type' => 'vehicle',
            'listing' => $vehicle->load('photos'),
        ]);
    }
}
