<?php

namespace App\Domains\Identity\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Identity — format conforme à API_GUIDE.md §6.
 *
 * @mixin \App\Domains\Identity\Models\User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'user',
            'attributes' => [
                'full_name' => $this->full_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            ],
        ];
    }
}
