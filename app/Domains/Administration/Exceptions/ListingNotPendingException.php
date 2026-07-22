<?php

namespace App\Domains\Administration\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée quand un administrateur tente de valider ou rejeter une annonce
 * (résidence ou véhicule) déjà dans un état terminal différent — voir
 * docs/engineering/09-api-guidelines.md §7/§8. Rejouer la même action sur
 * une annonce déjà dans l'état cible est idempotent et ne lève pas cette
 * exception.
 */
final class ListingNotPendingException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Cette annonce n\'est plus en attente de validation.');
    }

    public function apiStatus(): int
    {
        return 409;
    }

    public function apiErrorCode(): string
    {
        return 'listing_not_pending';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
