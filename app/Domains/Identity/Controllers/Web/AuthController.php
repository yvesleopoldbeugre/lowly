<?php

namespace App\Domains\Identity\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Domaine Identity — page de connexion/inscription, voir UX_UI.md §4.4-4.5
 * et docs/ux/mockups/03-connexion-inscription.html.
 *
 * Une seule vue pour les deux écrans (onglets Alpine) ; la mutation réelle
 * (POST /api/v1/auth/register|login) est gérée par resources/js/alpine/auth.js,
 * ce contrôleur ne fait que choisir l'onglet actif selon la route.
 */
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.public.auth', ['initialTab' => 'connexion', 'title' => 'Connexion']);
    }

    public function showRegister(): View
    {
        return view('pages.public.auth', ['initialTab' => 'inscription', 'title' => 'Inscription']);
    }
}
