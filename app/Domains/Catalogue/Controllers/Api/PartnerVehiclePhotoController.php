<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Actions\AddVehiclePhoto;
use App\Domains\Catalogue\Actions\RemoveVehiclePhoto;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Catalogue\Models\VehiclePhoto;
use App\Domains\Catalogue\Requests\StoreVehiclePhotoRequest;
use App\Domains\Catalogue\Resources\VehiclePhotoResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (gestion des photos véhicules,
 * symétrique de PartnerResidencePhotoController).
 */
class PartnerVehiclePhotoController extends Controller
{
    public function store(StoreVehiclePhotoRequest $request, Vehicle $vehicle, AddVehiclePhoto $action): JsonResponse
    {
        $this->authorize('managePhotos', $vehicle);

        $photo = $action->executer($vehicle, $request->file('photo'), $request->integer('position') ?: null);

        return VehiclePhotoResource::make($photo)->response()->setStatusCode(201);
    }

    public function destroy(Vehicle $vehicle, VehiclePhoto $photo, RemoveVehiclePhoto $action): Response
    {
        $this->authorize('managePhotos', $vehicle);

        abort_unless($photo->vehicle_id === $vehicle->id, 404);

        $action->executer($photo);

        return response()->noContent();
    }
}
