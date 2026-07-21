<?php

namespace App\Domains\Administration\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Domaine Administration — format conforme à API_GUIDE.md §6, DATABASE.md §10.2.
 *
 * @mixin \App\Domains\Administration\Models\PlatformSetting
 */
class PlatformSettingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'platform_setting',
            'attributes' => [
                'key' => $this->key,
                'value' => $this->value,
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
        ];
    }
}
