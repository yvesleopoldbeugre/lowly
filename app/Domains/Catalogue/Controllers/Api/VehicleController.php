<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Actions\ListPublishedVehicles;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Catalogue\Requests\ListVehiclesRequest;
use App\Domains\Catalogue\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9 (endpoints publics).
 */
class VehicleController extends Controller
{
    public function index(ListVehiclesRequest $request, ListPublishedVehicles $action): AnonymousResourceCollection
    {
        return VehicleResource::collection($action->executer($request->validated()));
    }

    public function show(Vehicle $vehicle): VehicleResource
    {
        abort_unless($vehicle->isPublished(), 404);

        return VehicleResource::make($vehicle->load('photos'));
    }
}
