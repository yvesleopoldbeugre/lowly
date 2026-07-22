<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Administration\Models\PlatformSetting;
use Illuminate\Database\Eloquent\Collection;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`GET /admin/settings`).
 */
final class ListPlatformSettings
{
    /**
     * @return Collection<int, PlatformSetting>
     */
    public function executer(): Collection
    {
        return PlatformSetting::query()->orderBy('key')->get();
    }
}
