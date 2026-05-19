<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ImpersonateBannerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Injeta variável global para o layout exibir o banner
        view()->share('isImpersonating', session()->has('impersonator_id'));
        view()->share('impersonatorName', session('impersonator_name', ''));
        view()->share('impersonatedCompany', session('impersonated_company', ''));

        return $next($request);
    }
}
