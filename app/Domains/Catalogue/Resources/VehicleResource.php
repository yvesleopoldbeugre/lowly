<?php

namespace App\Domains\Catalogue\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Catalogue — format conforme à API_GUIDE.md §6.1.
 *
 * @mixin \App\Domains\Catalogue\Models\Vehicle
 */
class VehicleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'vehicle',
            'attributes' => [
                'brand' => $this->brand,
                'model' => $this->model,
                'year' => $this->year,
                'plate_number' => $this->plate_number,
                'daily_rate' => $this->daily_rate,
                'attributes' => $this->attributes,
                'status' => $this->status,
            ],
            'relationships' => [
                'partner' => ['id' => $this->partner_id, 'type' => 'partner'],
            ],
            'photos' => VehiclePhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
