<?php

namespace App\Domains\Catalogue\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée par UpdateResidence/UpdateVehicle quand un partenaire non validé
 * tente de soumettre une annonce à validation (`submit_for_validation`) —
 * voir docs/engineering/09-api-guidelines.md §7.
 */
final class PartnerNotValidatedException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Votre profil partenaire doit être validé avant de soumettre une annonce.');
    }

    public function apiStatus(): int
    {
        return 409;
    }

    public function apiErrorCode(): string
    {
        return 'partner_not_validated';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
