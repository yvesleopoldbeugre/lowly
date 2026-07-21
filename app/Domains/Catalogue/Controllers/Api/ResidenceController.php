<?php

namespace App\Domains\Catalogue\Controllers\Api;

use App\Domains\Catalogue\Actions\ListPublishedResidences;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Requests\ListResidencesRequest;
use App\Domains\Catalogue\Resources\ResidenceResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Catalogue — voir API_GUIDE.md §9 (endpoints publics).
 */
class ResidenceController extends Controller
{
    public function index(ListResidencesRequest $request, ListPublishedResidences $action): AnonymousResourceCollection
    {
        return ResidenceResource::collection($action->executer($request->validated()));
    }

    public function show(Residence $residence): ResidenceResource
    {
        abort_unless($residence->isPublished(), 404);

        return ResidenceResource::make($residence->load('photos'));
    }
}
