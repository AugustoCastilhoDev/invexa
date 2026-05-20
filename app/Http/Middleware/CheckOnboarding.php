<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboarding
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->company_id) {
            return $next($request);
        }

        // Não redireciona em modo suporte (impersonate)
        if (session()->has('impersonator_id')) {
            return $next($request);
        }

        $company = $user->company;

        // Se onboarding não concluído e não está na rota do wizard
        if (
            $company &&
            ! $company->onboarding_completed &&
            ! $request->routeIs('onboarding.*') &&
            ! $request->routeIs('logout')
        ) {
            return redirect()->route('onboarding.show');
        }

        return $next($request);
    }
}
