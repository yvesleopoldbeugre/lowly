<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Administration\Actions\ListPlatformSettings;
use App\Domains\Administration\Actions\UpdatePlatformSetting;
use App\Domains\Administration\Requests\UpdateSettingRequest;
use App\Domains\Administration\Resources\PlatformSettingResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Administration — voir API_GUIDE.md §12, DATABASE.md §10.2, UX_UI.md §7.5.
 */
class AdminSettingController extends Controller
{
    public function index(Request $request, ListPlatformSettings $action): AnonymousResourceCollection
    {
        return PlatformSettingResource::collection($action->executer());
    }

    public function update(UpdateSettingRequest $request, UpdatePlatformSetting $action): PlatformSettingResource
    {
        return PlatformSettingResource::make($action->executer($request->validated()));
    }
}
