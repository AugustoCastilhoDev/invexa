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

        $prices = ['free' => 0, 'pro' => 39.90, 'business' => 119.90];
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

    public function changePlan(Request $request, Company $company)
    {
        $validated = $request->validate([
            'plan' => ['required', 'in:free,pro,business'],
        ]);

        $oldPlan = $company->plan;
        $company->update(['plan' => $validated['plan']]);

        return back()->with('success', "Plano da empresa \"{$company->name}\" alterado de {$oldPlan} para {$validated['plan']}.");
    }

    public function destroyCompany(Company $company)
    {
        $name = $company->name;
        User::where('company_id', $company->id)->delete();
        $company->delete();
        return back()->with('success', "Empresa \"{$name}\" e todos os seus dados foram removidos.");
    }

    public function impersonate(Company $company)
    {
        $roleOrder = ['admin', 'gerente', 'vendedor'];
        $target = null;
        foreach ($roleOrder as $role) {
            $target = User::where('company_id', $company->id)
                ->where('active', true)
                ->where('role', $role)
                ->first();
            if ($target) break;
        }

        if (! $target) {
            return back()->with('error', 'Nenhum usuário ativo encontrado nesta empresa.');
        }

        session([
            'impersonator_id'      => Auth::id(),
            'impersonator_name'    => Auth::user()->name,
            'impersonated_company' => $company->name,
        ]);

        Auth::login($target);

        return redirect()->route('dashboard')
            ->with('success', "Entrando como {$target->name} — {$company->name}.");
    }

    public function leaveImpersonate()
    {
        $impersonatorId = session('impersonator_id');

        if (! $impersonatorId) {
            return redirect()->route('admin.index');
        }

        $superAdmin = User::findOrFail($impersonatorId);
        session()->forget(['impersonator_id', 'impersonator_name', 'impersonated_company']);
        Auth::login($superAdmin);

        return redirect()->route('admin.index')
            ->with('success', 'Modo suporte encerrado. Bem-vindo de volta!');
    }
}
