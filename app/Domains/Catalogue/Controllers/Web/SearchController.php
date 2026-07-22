<?php

namespace App\Domains\Catalogue\Controllers\Web;

use App\Domains\Catalogue\Actions\ListPublishedResidences;
use App\Domains\Catalogue\Actions\ListPublishedVehicles;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Catalogue — page d'accueil + recherche, voir UX_UI.md §4.1-4.2 et
 * docs/ux/mockups/01-accueil-recherche.html.
 *
 * Réutilise directement les Actions de la tranche Catalogue (déjà
 * utilisées par ResidenceController/VehicleController Api) pour le premier
 * rendu ; le filtrage dynamique passe ensuite par resources/js/alpine/search.js
 * (GET /api/v1/search), sans recharger la page.
 */
class SearchController extends Controller
{
    public function index(Request $request, ListPublishedResidences $listResidences, ListPublishedVehicles $listVehicles): View
    {
        $type = $request->string('type', 'residence')->value();
        $type = in_array($type, ['residence', 'vehicle'], true) ? $type : 'residence';

        $filters = [
            'city' => $request->string('city')->value() ?: null,
            'min_price' => $request->filled('min_price') ? $request->float('min_price') : null,
            'max_price' => $request->filled('max_price') ? $request->float('max_price') : null,
            'capacity' => $request->filled('capacity') ? $request->integer('capacity') : null,
        ];

        $listings = $type === 'residence'
            ? $listResidences->executer($filters)
            : $listVehicles->executer($filters);

        return view('pages.public.home', [
            'title' => 'Résidences et véhicules',
            'type' => $type,
            'listings' => $listings,
            'filters' => $filters,
        ]);
    }
}
