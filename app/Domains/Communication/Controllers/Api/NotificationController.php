<?php

namespace App\Domains\Communication\Controllers\Api;

use App\Domains\Communication\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Communication — voir API_GUIDE.md §10, UX_UI.md §5.5.
 */
class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (GET /api/v1/notifications).');
    }

    public function markRead(Notification $notification): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §10 (PATCH /api/v1/notifications/{id}/read).');
    }
}
