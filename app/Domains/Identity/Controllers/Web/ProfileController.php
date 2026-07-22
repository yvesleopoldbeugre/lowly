<?php

namespace App\Domains\Identity\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Identity — page de profil, voir UX_UI.md §5.4.
 *
 * Accessible à tout rôle authentifié (client, partenaire, administrateur),
 * comme l'endpoint API équivalent GET /api/v1/me — voir routes/api.php.
 */
class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('pages.client.profile', ['user' => $request->user(), 'title' => 'Mon profil']);
    }
}
