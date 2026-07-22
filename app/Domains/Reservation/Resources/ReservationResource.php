<?php

namespace App\Domains\Reservation\Resources;

use App\Domains\Reservation\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Reservation — format conforme à API_GUIDE.md §6.2.
 *
 * @mixin Reservation
 */
class ReservationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'reservation',
            'attributes' => [
                'period' => [
                    'start' => $this->period['start']?->toDateString(),
                    'end' => $this->period['end']?->toDateString(),
                ],
                'nights_count' => $this->nights_count,
                'total_amount' => $this->total_amount,
                'status' => $this->status,
            ],
            'relationships' => [
                'client' => ['id' => $this->client_id, 'type' => 'user'],
                'reservable' => ['id' => $this->reservable_id, 'type' => $this->reservable_type],
                'parent_reservation' => $this->when(
                    $this->parent_reservation_id !== null,
                    fn () => ['id' => $this->parent_reservation_id, 'type' => 'reservation']
                ),
                'counter_offer' => $this->whenLoaded(
                    'counterOffer',
                    fn () => $this->counterOffer ? CounterOfferResource::make($this->counterOffer) : null,
                ),
            ],
        ];
    }
}
