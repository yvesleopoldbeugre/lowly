<?php

namespace App\Domains\Reservation\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée quand la période demandée (nouvelle demande ou bien alternatif
 * d'une contre-proposition) chevauche un blocage existant — voir
 * docs/engineering/09-api-guidelines.md §7 et BUSINESS_RULES.md §5.1/§6.2.
 */
final class ReservationPeriodUnavailableException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Cette période n\'est plus disponible pour ce bien.');
    }

    public function apiStatus(): int
    {
        return 409;
    }

    public function apiErrorCode(): string
    {
        return 'reservation_period_unavailable';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
