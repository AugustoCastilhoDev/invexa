<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalCompanies  = Company::count();
        $activeCompanies = Company::where('active', true)->count();

        $newThisMonth = Company::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $planCounts = Company::selectRaw('plan, count(*) as total')
            ->groupBy('plan')
            ->pluck('total', 'plan')
            ->toArray();

        $prices = ['free' => 0, 'pro' => 79, 'business' => 149];
        $mrr = 0;
        foreach ($planCounts as $plan => $count) {
            $mrr += ($prices[$plan] ?? 0) * $count;
        }

        $churnThisMonth = Company::where('active', false)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $companies = Company::withCount('users')->latest()->paginate(20);

        return view('superadmin.index', compact(
            'totalCompanies', 'activeCompanies', 'newThisMonth',
            'mrr', 'churnThisMonth', 'planCounts', 'companies'
        ));
    }

    public function toggleCompany(Company $company)
    {
        $company->update(['active' => !$company->active]);
        $status = $company->active ? 'ativada' : 'desativada';
        return back()->with('success', "Empresa \"{$company->name}\" {$status} com sucesso.");
    }

    // ── IMPERSONATE ──

    public function impersonate(Company $company)
    {
        // Pega o primeiro admin ativo da empresa
        $target = User::where('company_id', $company->id)
            ->where('active', true)
            ->whereIn('role', ['admin', 'gerente', 'vendedor'])
            ->orderByRaw("FIELD(role, 'admin', 'gerente', 'vendedor')")
            ->firstOrFail();

        // Guarda o ID do superadmin na sessão
        session([
            'impersonator_id'      => Auth::id(),
            'impersonator_name'    => Auth::user()->name,
            'impersonated_company' => $company->name,
        ]);

        Auth::login($target);

        return redirect()->route('home')
            ->with('success', "Entrando como {$target->name} — {$company->name}.");
    }

    public function leaveImpersonate()
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('admin.index');
        }

        $superAdmin = User::findOrFail($impersonatorId);

        session()->forget(['impersonator_id', 'impersonator_name', 'impersonated_company']);

        Auth::login($superAdmin);

        return redirect()->route('admin.index')
            ->with('success', 'Modo suporte encerrado. Bem-vindo de volta!');
    }
}
