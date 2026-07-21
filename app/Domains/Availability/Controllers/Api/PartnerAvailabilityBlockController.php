<?php

namespace App\Domains\Availability\Controllers\Api;

use App\Domains\Availability\Actions\CreateAvailabilityBlock;
use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Availability\Requests\StoreAvailabilityBlockRequest;
use App\Domains\Availability\Resources\AvailabilityBlockResource;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Domaine Availability — voir API_GUIDE.md §11, BUSINESS_RULES.md §4.2 et §7.
 */
class PartnerAvailabilityBlockController extends Controller
{
    public function store(StoreAvailabilityBlockRequest $request, CreateAvailabilityBlock $action): JsonResponse
    {
        $data = $request->validated();

        $blockable = $data['blockable_type'] === 'residence'
            ? Residence::findOrFail($data['blockable_id'])
            : Vehicle::findOrFail($data['blockable_id']);

        $this->authorize('create', [AvailabilityBlock::class, $blockable]);

        $block = $action->executer($blockable, $request->user(), $data);

        return AvailabilityBlockResource::make($block)->response()->setStatusCode(201);
    }

    public function destroy(AvailabilityBlock $availabilityBlock): Response
    {
        $this->authorize('delete', $availabilityBlock);

        $availabilityBlock->delete();

        return response()->noContent();
    }
}
