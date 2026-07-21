<?php

namespace App\Support\Exceptions;

/**
 * Contrat commun à toute exception de domaine devant produire une réponse
 * d'erreur API conforme à l'enveloppe unique définie par API_GUIDE.md §7 :
 * `{"error": {"code", "message", "details"}}`. Voir le rendu générique dans
 * bootstrap/app.php (withExceptions).
 */
interface ApiError
{
    public function apiStatus(): int;

    public function apiErrorCode(): string;

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array;
}
