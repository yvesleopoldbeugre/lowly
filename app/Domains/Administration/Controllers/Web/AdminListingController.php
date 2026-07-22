<?php

namespace App\Domains\Administration\Controllers\Web;

use App\Domains\Administration\Actions\ListPendingListings;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Administration — file de validation des annonces, voir
 * UX_UI.md §7.2 et docs/ux/mockups/07-validation-admin.html.
 */
class AdminListingController extends Controller
{
    public function index(Request $request, ListPendingListings $action): View
    {
        return view('pages.admin.listings.index', [
            'title' => 'Annonces',
            'listings' => $action->executer(),
        ]);
    }
}
