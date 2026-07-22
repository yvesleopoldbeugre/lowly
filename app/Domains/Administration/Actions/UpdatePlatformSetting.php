<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Administration\Models\PlatformSetting;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`PATCH /admin/settings`).
 * La clé est déjà validée par `UpdateSettingRequest` (`exists:platform_settings,key`).
 */
final class UpdatePlatformSetting
{
    /**
     * @param  array{key: string, value: mixed}  $data
     */
    public function executer(array $data): PlatformSetting
    {
        $setting = PlatformSetting::query()->where('key', $data['key'])->firstOrFail();

        $setting->update(['value' => $data['value']]);

        return $setting;
    }
}
