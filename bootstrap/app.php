<?php

/**
 * Laravel 11 — bootstrap/app.php
 *
 * Registra os dois novos middlewares com aliases
 * para uso nas rotas.
 */

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureHasCompany;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Aliases — use nas rotas como string curta
        $middleware->alias([
            'role'    => CheckRole::class,
            'company' => EnsureHasCompany::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();