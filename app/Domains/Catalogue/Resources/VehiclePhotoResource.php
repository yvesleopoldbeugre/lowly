<?php

namespace App\Domains\Catalogue\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Catalogue — format conforme à API_GUIDE.md §6.
 *
 * @mixin \App\Domains\Catalogue\Models\VehiclePhoto
 */
class VehiclePhotoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'vehicle_photo',
            'attributes' => [
                'path' => $this->path,
                'position' => $this->position,
            ],
        ];
    }
}
