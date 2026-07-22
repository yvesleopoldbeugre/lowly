<?php

namespace App\Domains\Communication\Controllers\Api;

use App\Domains\Communication\Actions\ListUserNotifications;
use App\Domains\Communication\Actions\MarkNotificationRead;
use App\Domains\Communication\Models\Notification;
use App\Domains\Communication\Resources\NotificationResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Domaine Communication — voir API_GUIDE.md §10, UX_UI.md §5.5.
 */
class NotificationController extends Controller
{
    public function index(Request $request, ListUserNotifications $action): AnonymousResourceCollection
    {
        return NotificationResource::collection(
            $action->executer($request->user(), ['per_page' => $request->integer('per_page')])
        );
    }

    public function markRead(Notification $notification, MarkNotificationRead $action): NotificationResource
    {
        $this->authorize('view', $notification);

        return NotificationResource::make($action->executer($notification));
    }
}
