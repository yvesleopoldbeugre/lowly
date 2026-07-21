<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Actions\CreateResidence;
use App\Domains\Catalogue\Actions\ListPartnerResidences;
use App\Domains\Catalogue\Actions\UpdateResidence;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Requests\StoreResidenceRequest;
use App\Domains\Catalogue\Requests\UpdateResidenceRequest;
use App\Domains\Catalogue\Resources\ResidenceResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (résidences du partenaire connecté).
 */
class PartnerResidenceController extends Controller
{
    public function index(Request $request, ListPartnerResidences $action): AnonymousResourceCollection
    {
        return ResidenceResource::collection(
            $action->executer($request->user()->partner, ['per_page' => $request->integer('per_page')])
        );
    }

    public function store(StoreResidenceRequest $request, CreateResidence $action): JsonResponse
    {
        $this->authorize('create', Residence::class);

        $residence = $action->executer($request->user()->partner, $request->validated());

        return ResidenceResource::make($residence)->response()->setStatusCode(201);
    }

    public function update(UpdateResidenceRequest $request, Residence $residence, UpdateResidence $action): ResidenceResource
    {
        $this->authorize('update', $residence);

        return ResidenceResource::make($action->executer($residence, $request->validated()));
    }
}
