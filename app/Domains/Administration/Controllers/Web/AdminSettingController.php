<?php

namespace App\Domains\Administration\Controllers\Web;

use App\Domains\Administration\Actions\ListPlatformSettings;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Administration — paramètres de la plateforme, voir
 * DATABASE.md §10.2, UX_UI.md §7.5.
 */
class AdminSettingController extends Controller
{
    public function index(Request $request, ListPlatformSettings $action): View
    {
        return view('pages.admin.settings.index', [
            'title' => 'Paramètres',
            'settings' => $action->executer(),
        ]);
    }
}
