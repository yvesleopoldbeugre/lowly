<?php

namespace App\Domains\Catalogue\Controllers\Web;

use App\Domains\Catalogue\Actions\ListPartnerResidences;
use App\Domains\Catalogue\Models\Residence;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Catalogue — gestion des résidences du partenaire connecté, voir
 * UX_UI.md §6.2.
 */
class PartnerResidenceController extends Controller
{
    public function index(Request $request, ListPartnerResidences $action): View
    {
        return view('pages.partner.residences.index', [
            'title' => 'Mes résidences',
            'residences' => $action->executer($request->user()->partner, []),
        ]);
    }

    public function create(): View
    {
        return view('pages.partner.residences.form', [
            'title' => 'Nouvelle résidence',
            'residence' => null,
        ]);
    }

    public function edit(Residence $residence): View
    {
        $this->authorize('update', $residence);

        return view('pages.partner.residences.form', [
            'title' => 'Modifier '.$residence->title,
            'residence' => $residence->load('photos'),
        ]);
    }
}
