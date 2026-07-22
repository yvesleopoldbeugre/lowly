<?php

namespace App\Domains\Reservation\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée par ReservationPolicy quand un partenaire tente d'agir (accepter,
 * refuser, contre-proposer) sur une réservation qui ne concerne pas l'un
 * de ses biens — voir docs/engineering/09-api-guidelines.md §7. Jetée
 * directement depuis la Policy plutôt que via un simple `return false`
 * pour porter ce code métier spécifique plutôt que le `forbidden` générique.
 */
final class ReservationNotOwnedException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Cette réservation ne concerne pas l\'un de vos biens.');
    }

    public function apiStatus(): int
    {
        return 403;
    }

    public function apiErrorCode(): string
    {
        return 'reservation_not_owned';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
