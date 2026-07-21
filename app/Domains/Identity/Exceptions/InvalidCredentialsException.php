<?php

namespace App\Domains\Identity\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée par LoginUser en cas d'échec d'authentification — email inconnu ou
 * mot de passe erroné. Le message ne distingue volontairement pas les deux
 * cas, pour ne pas révéler si un email existe en base (voir
 * docs/engineering/10-security-guidelines.md).
 */
final class InvalidCredentialsException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Email ou mot de passe incorrect.');
    }

    public function apiStatus(): int
    {
        return 401;
    }

    public function apiErrorCode(): string
    {
        return 'invalid_credentials';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
