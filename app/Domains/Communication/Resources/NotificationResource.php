<?php

namespace App\Domains\Communication\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Communication — format conforme à API_GUIDE.md §6.
 *
 * @mixin \App\Domains\Communication\Models\Notification
 */
class NotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'notification',
            'attributes' => [
                'notification_type' => $this->type,
                'payload' => $this->payload,
                'read_at' => $this->read_at?->toIso8601String(),
                'created_at' => $this->created_at?->toIso8601String(),
            ],
        ];
    }
}
