<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Administration\Actions\ListPendingPartners;
use App\Domains\Administration\Actions\RejeterPartenaireAction;
use App\Domains\Administration\Actions\ValiderPartenaireAction;
use App\Domains\Administration\Requests\RejectPartnerRequest;
use App\Domains\Partners\Models\Partner;
use App\Domains\Partners\Resources\PartnerResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Administration — voir API_GUIDE.md §12, UX_UI.md §7.1.
 */
class AdminPartnerController extends Controller
{
    public function pending(Request $request, ListPendingPartners $action): AnonymousResourceCollection
    {
        return PartnerResource::collection($action->executer(['per_page' => $request->integer('per_page')]));
    }

    public function validatePartner(Partner $partner, Request $request, ValiderPartenaireAction $action): PartnerResource
    {
        return PartnerResource::make($action->executer($partner, $request->user()));
    }

    public function reject(RejectPartnerRequest $request, Partner $partner, RejeterPartenaireAction $action): PartnerResource
    {
        return PartnerResource::make($action->executer($partner, $request->user(), $request->validated()));
    }
}
