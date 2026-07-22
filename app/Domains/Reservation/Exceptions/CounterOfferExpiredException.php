<?php

namespace App\Domains\Reservation\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée quand le client répond à une contre-proposition après l'expiration
 * de son délai de réponse — voir BUSINESS_RULES.md §6.2,
 * docs/engineering/09-api-guidelines.md §7.
 */
final class CounterOfferExpiredException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Cette contre-proposition a expiré.');
    }

    public function apiStatus(): int
    {
        return 409;
    }

    public function apiErrorCode(): string
    {
        return 'counter_offer_expired';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
