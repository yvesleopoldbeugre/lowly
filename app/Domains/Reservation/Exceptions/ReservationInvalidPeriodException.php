<?php

namespace App\Domains\Reservation\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée quand une période résout à zéro journée facturée (arrivée et
 * départ le même jour calendaire — BUSINESS_RULES.md §10) — garde
 * défensive côté Action en complément de la validation de FormRequest,
 * nécessaire pour la contre-proposition dont les dates sont optionnelles
 * (voir StoreCounterOfferRequest, BUSINESS_RULES.md §6.1).
 */
final class ReservationInvalidPeriodException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('La date de départ doit être postérieure à la date d\'arrivée.');
    }

    public function apiStatus(): int
    {
        return 422;
    }

    public function apiErrorCode(): string
    {
        return 'reservation_invalid_period';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
