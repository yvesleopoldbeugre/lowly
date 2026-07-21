<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Support\Exceptions\ApiError;
use App\Support\Exceptions\ApiErrorResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ThrottleRequestsException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Enveloppe d'erreur homogène (API_GUIDE.md §7-8) : toute exception de
        // domaine implémentant ApiError (ex : InvalidCredentialsException du
        // domaine Identity) prévaut sur les cas génériques ci-dessous.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($e instanceof ApiError) {
                return ApiErrorResponse::make($e->apiStatus(), $e->apiErrorCode(), $e->getMessage(), $e->apiErrorDetails());
            }

            if ($e instanceof ValidationException) {
                return ApiErrorResponse::make(422, 'validation_failed', $e->getMessage(), $e->errors());
            }

            if ($e instanceof AuthenticationException) {
                return ApiErrorResponse::make(401, 'unauthenticated', $e->getMessage());
            }

            if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                return ApiErrorResponse::make(403, 'forbidden', $e->getMessage() ?: 'Action non autorisée.');
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return ApiErrorResponse::make(404, 'not_found', 'Ressource introuvable.');
            }

            if ($e instanceof ThrottleRequestsException) {
                $response = ApiErrorResponse::make(429, 'too_many_requests', 'Trop de tentatives, réessayez plus tard.');

                foreach ($e->getHeaders() as $header => $value) {
                    $response->headers->set($header, $value);
                }

                return $response;
            }

            return null;
        });
    })->create();
