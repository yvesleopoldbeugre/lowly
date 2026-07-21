<?php

namespace App\Domains\Partners\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Partners — format conforme à API_GUIDE.md §6, DATABASE.md §5.1.
 *
 * @mixin \App\Domains\Partners\Models\Partner
 */
class PartnerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'partner',
            'attributes' => [
                'company_name' => $this->company_name,
                'status' => $this->status,
                'validated_at' => $this->validated_at?->toIso8601String(),
            ],
            'relationships' => [
                'user' => ['id' => $this->user_id, 'type' => 'user'],
            ],
        ];
    }
}
