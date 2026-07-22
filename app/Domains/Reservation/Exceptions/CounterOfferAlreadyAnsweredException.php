<?php

namespace App\Domains\Reservation\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée quand le client répond une seconde fois à une contre-proposition
 * déjà traitée — voir BUSINESS_RULES.md §6.2,
 * docs/engineering/09-api-guidelines.md §7.
 */
final class CounterOfferAlreadyAnsweredException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Cette contre-proposition a déjà été traitée.');
    }

    public function apiStatus(): int
    {
        return 409;
    }

    public function apiErrorCode(): string
    {
        return 'counter_offer_already_answered';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
