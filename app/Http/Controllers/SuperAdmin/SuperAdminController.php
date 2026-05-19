<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SuperAdminController extends Controller
{
    public function index()
    {
        // Métricas gerais
        $totalCompanies  = Company::count();
        $activeCompanies = Company::where('active', true)->count();

        $newThisMonth = Company::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Planos ativos (excluindo free/trial)
        $planCounts = Company::selectRaw('plan, count(*) as total')
            ->groupBy('plan')
            ->pluck('total', 'plan')
            ->toArray();

        // MRR estimado com base nos planos
        $prices = ['free' => 0, 'pro' => 79, 'business' => 149];
        $mrr = 0;
        foreach ($planCounts as $plan => $count) {
            $mrr += ($prices[$plan] ?? 0) * $count;
        }

        // Churn: empresas desativadas no mês
        $churnThisMonth = Company::where('active', false)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // Lista paginada de empresas
        $companies = Company::withCount('users')
            ->latest()
            ->paginate(20);

        return view('superadmin.index', compact(
            'totalCompanies',
            'activeCompanies',
            'newThisMonth',
            'mrr',
            'churnThisMonth',
            'planCounts',
            'companies'
        ));
    }

    public function toggleCompany(Company $company)
    {
        $company->update(['active' => !$company->active]);

        $status = $company->active ? 'ativada' : 'desativada';
        return back()->with('success', "Empresa \"" . $company->name . "\" {$status} com sucesso.");
    }
}
