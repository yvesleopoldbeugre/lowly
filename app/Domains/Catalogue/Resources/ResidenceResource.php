<?php

namespace App\Domains\Catalogue\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Catalogue — format conforme à API_GUIDE.md §6.1.
 *
 * @mixin \App\Domains\Catalogue\Models\Residence
 */
class ResidenceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'residence',
            'attributes' => [
                'title' => $this->title,
                'description' => $this->description,
                'address' => $this->address,
                'city' => $this->city,
                'capacity' => $this->capacity,
                'daily_rate' => $this->daily_rate,
                'attributes' => $this->attributes,
                'status' => $this->status,
            ],
            'relationships' => [
                'partner' => ['id' => $this->partner_id, 'type' => 'partner'],
            ],
            'photos' => ResidencePhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
