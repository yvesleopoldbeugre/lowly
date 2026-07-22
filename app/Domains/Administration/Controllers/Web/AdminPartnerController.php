<?php

namespace App\Domains\Administration\Controllers\Web;

use App\Domains\Administration\Actions\ListPendingPartners;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Administration — file de validation des partenaires, voir
 * UX_UI.md §7.1 et docs/ux/mockups/07-validation-admin.html.
 */
class AdminPartnerController extends Controller
{
    public function index(Request $request, ListPendingPartners $action): View
    {
        return view('pages.admin.partners.index', [
            'title' => 'Partenaires',
            'partners' => $action->executer([]),
        ]);
    }
}
