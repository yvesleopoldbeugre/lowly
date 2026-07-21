<?php

namespace App\Domains\Availability\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée par CreateAvailabilityBlock quand la contrainte d'exclusion GiST
 * PostgreSQL (`excl_availability_no_overlap`, voir DATABASE.md §7.2 et
 * BUSINESS_RULES.md §7.2) rejette un chevauchement de période.
 */
final class AvailabilityBlockOverlapException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Cette période chevauche un blocage existant sur ce bien.');
    }

    public function apiStatus(): int
    {
        return 409;
    }

    public function apiErrorCode(): string
    {
        return 'availability_block_overlap';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
