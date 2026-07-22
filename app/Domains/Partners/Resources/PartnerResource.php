<?php

namespace App\Domains\Partners\Resources;

use App\Domains\Partners\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Partners — format conforme à API_GUIDE.md §6, DATABASE.md §5.1.
 *
 * @mixin Partner
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
                'has_legal_document' => $this->legal_document_path !== null,
            ],
            'relationships' => [
                'user' => ['id' => $this->user_id, 'type' => 'user'],
            ],
        ];
    }
}
