<?php

namespace App\Domains\Communication\Controllers\Web;

use App\Domains\Communication\Actions\ListUserNotifications;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Communication — liste des notifications de l'utilisateur
 * connecté (client, partenaire ou administrateur), voir UX_UI.md §5.5.
 */
class NotificationController extends Controller
{
    public function index(Request $request, ListUserNotifications $action): View
    {
        return view('pages.shared.notifications.index', [
            'title' => 'Notifications',
            'notifications' => $action->executer($request->user(), []),
        ]);
    }
}
