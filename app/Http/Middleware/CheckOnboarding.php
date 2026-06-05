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

        if (! $user) {
            return $next($request);
        }

        // SuperAdmin nunca precisa de onboarding
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        $company = $user->company;

        if ($company && ! $company->onboarding_completed && ! $request->routeIs('onboarding.*', 'logout')) {
            return redirect()->route('onboarding.show');
        }

        return $next($request);
    }
}
