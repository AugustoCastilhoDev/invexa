<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Adiciona o banner de impersonate em todas as requisições web
        $middleware->web(append: [
            \App\Http\Middleware\ImpersonateBannerMiddleware::class,
        ]);

        $middleware->alias([
            'role'        => \App\Http\Middleware\CheckRole::class,
            'company'     => \App\Http\Middleware\CompanyMiddleware::class,
            'trial'       => \App\Http\Middleware\CheckCompanyAccess::class,
            'superadmin'  => \App\Http\Middleware\SuperAdminMiddleware::class,
            'onboarding'  => \App\Http\Middleware\CheckOnboarding::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
