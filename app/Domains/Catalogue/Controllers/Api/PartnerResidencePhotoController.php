<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Actions\AddResidencePhoto;
use App\Domains\Catalogue\Actions\RemoveResidencePhoto;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\ResidencePhoto;
use App\Domains\Catalogue\Requests\StoreResidencePhotoRequest;
use App\Domains\Catalogue\Resources\ResidencePhotoResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (gestion des photos, UX_UI.md §6.6).
 */
class PartnerResidencePhotoController extends Controller
{
    public function store(StoreResidencePhotoRequest $request, Residence $residence, AddResidencePhoto $action): JsonResponse
    {
        $this->authorize('managePhotos', $residence);

        $photo = $action->executer($residence, $request->file('photo'), $request->integer('position') ?: null);

        return ResidencePhotoResource::make($photo)->response()->setStatusCode(201);
    }

    public function destroy(Residence $residence, ResidencePhoto $photo, RemoveResidencePhoto $action): Response
    {
        $this->authorize('managePhotos', $residence);

        abort_unless($photo->residence_id === $residence->id, 404);

        $action->executer($photo);

        return response()->noContent();
    }
}
