<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Actions\ListPublishedResidences;
use App\Domains\Catalogue\Actions\ListPublishedVehicles;
use App\Domains\Catalogue\Requests\SearchCatalogueRequest;
use App\Domains\Catalogue\Resources\ResidenceResource;
use App\Domains\Catalogue\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9.
 *
 * Recherche transverse résidences + véhicules, une catégorie à la fois
 * (`type` obligatoire — voir SearchCatalogueRequest). Réutilise les mêmes
 * Actions que ResidenceController::index/VehicleController::index plutôt que
 * de dupliquer la logique de filtrage.
 */
class SearchController extends Controller
{
    public function index(
        SearchCatalogueRequest $request,
        ListPublishedResidences $listResidences,
        ListPublishedVehicles $listVehicles,
    ): AnonymousResourceCollection {
        $filters = $request->validated();

        return $filters['type'] === 'residence'
            ? ResidenceResource::collection($listResidences->executer($filters))
            : VehicleResource::collection($listVehicles->executer($filters));
    }
}
