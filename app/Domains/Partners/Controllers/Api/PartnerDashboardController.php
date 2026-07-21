<?php

namespace App\Domains\Partners\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Partners — voir API_GUIDE.md §11, UX_UI.md §6.1.
 *
 * Agrège des données de plusieurs domaines (Reservation, Catalogue) : la
 * lecture inter-domaines en synthèse (hors flux d'événements métier) reste
 * acceptable pour un tableau de bord — voir ARCHITECTURE.md §14, la règle
 * de dépendances concerne le sens d'écriture/logique métier.
 */
class PartnerDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort(501, 'Non implémenté — voir API_GUIDE.md §11 (GET /api/v1/partner/dashboard).');
    }
}
