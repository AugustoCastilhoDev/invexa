<?php

/**
 * Laravel 11 — bootstrap/app.php
 */

use App\Http\Middleware\CheckCompanyAccess;
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

        $middleware->alias([
            'role'           => CheckRole::class,
            'company'        => EnsureHasCompany::class,
            'trial'          => CheckCompanyAccess::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
