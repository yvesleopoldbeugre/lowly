<?php

namespace App\Domains\Administration\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée quand un administrateur tente de valider ou rejeter un partenaire
 * déjà dans un état terminal différent (ex : rejeter un partenaire déjà
 * validé) — voir docs/engineering/09-api-guidelines.md §7/§8. Rejouer la
 * même action sur un partenaire déjà dans l'état cible est en revanche
 * idempotent et ne lève pas cette exception.
 */
final class PartnerNotPendingException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Ce partenaire n\'est plus en attente de validation.');
    }

    public function apiStatus(): int
    {
        return 409;
    }

    public function apiErrorCode(): string
    {
        return 'partner_not_pending';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
