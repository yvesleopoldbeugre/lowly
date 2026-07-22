<?php

namespace App\Domains\Catalogue\Controllers\Web;

use App\Domains\Catalogue\Models\Residence;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Domaine Catalogue — détail d'une résidence, voir UX_UI.md §4.3 et
 * docs/ux/mockups/02-detail-annonce.html.
 */
class ResidenceController extends Controller
{
    public function show(Residence $residence): View
    {
        abort_unless($residence->isPublished(), 404);

        return view('pages.public.listing-detail', [
            'title' => $residence->title,
            'type' => 'residence',
            'listing' => $residence->load('photos'),
        ]);
    }
}
