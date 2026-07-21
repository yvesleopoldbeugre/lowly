<?php

namespace App\Domains\Availability\Resources;

use App\Domains\Availability\Models\AvailabilityBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Availability — format conforme à API_GUIDE.md §6.
 *
 * @mixin AvailabilityBlock
 */
class AvailabilityBlockResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'availability_block',
            'attributes' => [
                'blockable_type' => $this->blockable_type,
                'period' => [
                    'start' => $this->period['start']->toDateString(),
                    'end' => $this->period['end']->toDateString(),
                ],
                'origin' => $this->origin,
            ],
            'relationships' => [
                'blockable' => ['id' => $this->blockable_id, 'type' => $this->blockable_type],
                'reservation' => $this->when($this->reservation_id !== null, [
                    'id' => $this->reservation_id,
                    'type' => 'reservation',
                ]),
                'created_by' => ['id' => $this->created_by, 'type' => 'user'],
            ],
        ];
    }
}
