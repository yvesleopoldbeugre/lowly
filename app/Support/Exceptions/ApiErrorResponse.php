<?php

namespace App\Support\Exceptions;

use Illuminate\Http\JsonResponse;

/**
 * Construit l'enveloppe d'erreur unique exigée par API_GUIDE.md §7 pour
 * toutes les routes `api/*` : {"error": {"code", "message", "details"}}.
 * Voir le rendu global des exceptions dans bootstrap/app.php.
 */
final class ApiErrorResponse
{
    /**
     * @param  array<string, mixed>  $details
     */
    public static function make(int $status, string $code, string $message, array $details = []): JsonResponse
    {
        return new JsonResponse([
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
        ], $status);
    }
}
