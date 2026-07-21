<?php

namespace App\Domains\Reservation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Reservation — format conforme à API_GUIDE.md §6, BUSINESS_RULES.md §6.
 *
 * @mixin \App\Domains\Reservation\Models\CounterOffer
 */
class CounterOfferResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'counter_offer',
            'attributes' => [
                'proposed_period' => [
                    'start' => $this->proposed_period['start']?->toDateString(),
                    'end' => $this->proposed_period['end']?->toDateString(),
                ],
                'status' => $this->status,
                'expires_at' => $this->expires_at?->toIso8601String(),
            ],
            'relationships' => [
                'original_reservation' => ['id' => $this->original_reservation_id, 'type' => 'reservation'],
                'proposed_reservable' => ['id' => $this->proposed_reservable_id, 'type' => $this->proposed_reservable_type],
            ],
        ];
    }
}
