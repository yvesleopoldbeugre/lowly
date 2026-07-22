<?php

namespace App\Domains\Identity\Exceptions;

use App\Support\Exceptions\ApiError;
use Exception;

/**
 * Levée par LoginUser quand les identifiants sont valides mais que le
 * compte a été suspendu par un administrateur (`users.suspended_at`) —
 * voir PRODUCT.md §9.4, API_GUIDE.md §12
 * (`PATCH /api/v1/admin/users/{id}/suspend`).
 */
final class AccountSuspendedException extends Exception implements ApiError
{
    public function __construct()
    {
        parent::__construct('Ce compte a été suspendu.');
    }

    public function apiStatus(): int
    {
        return 403;
    }

    public function apiErrorCode(): string
    {
        return 'account_suspended';
    }

    /**
     * @return array<string, mixed>
     */
    public function apiErrorDetails(): array
    {
        return [];
    }
}
