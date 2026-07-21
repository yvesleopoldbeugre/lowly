<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Actions\CreateVehicle;
use App\Domains\Catalogue\Actions\ListPartnerVehicles;
use App\Domains\Catalogue\Actions\UpdateVehicle;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Catalogue\Requests\StoreVehicleRequest;
use App\Domains\Catalogue\Requests\UpdateVehicleRequest;
use App\Domains\Catalogue\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (véhicules du partenaire connecté).
 */
class PartnerVehicleController extends Controller
{
    public function index(Request $request, ListPartnerVehicles $action): AnonymousResourceCollection
    {
        return VehicleResource::collection(
            $action->executer($request->user()->partner, ['per_page' => $request->integer('per_page')])
        );
    }

    public function store(StoreVehicleRequest $request, CreateVehicle $action): JsonResponse
    {
        $this->authorize('create', Vehicle::class);

        $vehicle = $action->executer($request->user()->partner, $request->validated());

        return VehicleResource::make($vehicle)->response()->setStatusCode(201);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle, UpdateVehicle $action): VehicleResource
    {
        $this->authorize('update', $vehicle);

        return VehicleResource::make($action->executer($vehicle, $request->validated()));
    }
}
