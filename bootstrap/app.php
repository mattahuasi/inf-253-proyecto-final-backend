<?php

use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::prefix('api')
                ->middleware(['api', ValidateJsonApiHeaders::class, ValidateJsonApiDocument::class])
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'guest' => App\Http\Middleware\MyRedirectIfAuthenticated::class,
            'auth.apiKey' => \App\Http\Middleware\ApiKeyMiddleware::class,
            'auth.status' => \App\Http\Middleware\EnsureUserIsEnabledMiddleware::class,
        ]);
        $middleware->statefulApi();
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                if (!$request->is('api/login') && !$request->is('api/register')) {
                    throw new \App\Exceptions\JsonApi\ValidationException($e);
                }
            }
        });
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*'))
                throw new \App\Exceptions\JsonApi\AuthenticationException($e->getMessage());
        });
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*'))
                throw new \App\Exceptions\JsonApi\AccessDeniedHttpException($e->getMessage());
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*'))
                throw new \App\Exceptions\JsonApi\NotFoundHttpException($e->getMessage());
        });
        $exceptions->render(function (BadRequestHttpException $e, Request $request) {
            if ($request->is('api/*'))
                throw new \App\Exceptions\JsonApi\BadRequestHttpException($e->getMessage());
        });
        $exceptions->render(function (NotAcceptableHttpException $e, Request $request) {
            if ($request->is('api/*'))
                throw new \App\Exceptions\JsonApi\NotAcceptableHttpException($e->getMessage());
        });
        $exceptions->render(function (UnsupportedMediaTypeHttpException $e, Request $request) {
            if ($request->is('api/*'))
                throw new \App\Exceptions\JsonApi\UnsupportedMediaTypeHttpException($e->getMessage());
        });
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*'))
                throw new \App\Exceptions\JsonApi\MethodNotAllowedHttpException($e->getMessage());
        });
    })->create();
