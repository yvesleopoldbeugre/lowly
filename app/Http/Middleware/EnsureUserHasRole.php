<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que l'utilisateur authentifié possède l'un des rôles autorisés
 * à accéder à la route (`role:partner`, `role:admin`, ...).
 *
 * Il s'agit d'une vérification de premier niveau au routage. L'autorisation
 * fine par ressource (ex : un partenaire ne peut agir que sur ses propres
 * biens) reste portée par les Policies de chaque domaine — voir
 * ARCHITECTURE.md §7 — à implémenter en phase Développement.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Accès réservé aux rôles : '.implode(', ', $roles).'.');
        }

        return $next($request);
    }
}
