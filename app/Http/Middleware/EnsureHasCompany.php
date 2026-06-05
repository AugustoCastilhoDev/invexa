<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->active) {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Sua conta está inativa.',
            ]);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (empty($user->company_id)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sua conta não está vinculada a nenhuma empresa.',
            ]);
        }

        return $next($request);
    }
}