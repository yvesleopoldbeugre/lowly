<?php

namespace App\Domains\Partners\Controllers\Web;

use App\Domains\Partners\Actions\GetPartnerDashboard;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Partners — tableau de bord, voir UX_UI.md §6.1 et
 * docs/ux/mockups/05-dashboard-partenaire.html.
 */
class DashboardController extends Controller
{
    public function index(Request $request, GetPartnerDashboard $action): View
    {
        return view('pages.partner.dashboard', [
            'title' => 'Tableau de bord',
            'stats' => $action->executer($request->user()->partner),
        ]);
    }
}
