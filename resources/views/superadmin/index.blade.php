<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — Invexa</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%23080D1A'/%3E%3Cpath d='M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z' fill='%230EA5E9'/%3E%3Ccircle cx='24' cy='10' r='2.2' fill='%2338BDF8'/%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --abyss:#080D1A; --navy:#0D1929; --sky:#0EA5E9; --elec:#38BDF8; --ice:#F0F9FF; }
        body { background: var(--abyss); color: #e2e8f0; font-family: system-ui, sans-serif; }
        .sa-topbar { background:rgba(8,13,26,.95); border-bottom:1px solid rgba(14,165,233,.15); padding:.65rem 1.5rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:1000; backdrop-filter:blur(12px); }
        .sa-brand { display:flex; align-items:center; gap:.6rem; font-weight:800; color:var(--ice); font-size:.95rem; }
        .sa-badge { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.3); color:#f87171; font-size:.65rem; font-weight:700; padding:.1rem .55rem; border-radius:999px; text-transform:uppercase; letter-spacing:.08em; }
        .metric-card { background:rgba(13,25,41,.8); border:1px solid rgba(14,165,233,.1); border-radius:14px; padding:24px 22px; transition:border-color .2s; }
        .metric-card:hover { border-color:rgba(14,165,233,.3); }
        .metric-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:rgba(148,163,184,.6); margin-bottom:.35rem; }
        .metric-value { font-size:2rem; font-weight:800; color:var(--ice); line-height:1; }
        .metric-sub   { font-size:.75rem; color:rgba(148,163,184,.55); margin-top:.25rem; }
        .metric-icon  { font-size:1.4rem; color:var(--sky); opacity:.7; }
        .sa-section-title { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--sky); margin-bottom:.75rem; }
        .sa-table { width:100%; border-collapse:collapse; font-size:.875rem; }
        .sa-table thead th { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(148,163,184,.5); padding:.6rem 1rem; border-bottom:1px solid rgba(14,165,233,.1); background:rgba(13,25,41,.6); }
        .sa-table tbody tr { border-bottom:1px solid rgba(14,165,233,.06); transition:background .15s; }
        .sa-table tbody tr:hover { background:rgba(14,165,233,.04); }
        .sa-table tbody td { padding:.75rem 1rem; color:rgba(226,232,240,.8); vertical-align:middle; }
        .sa-table tbody tr:last-child { border-bottom:none; }
        .plan-badge { display:inline-block; padding:.15rem .6rem; border-radius:999px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
        .plan-free     { background:rgba(148,163,184,.1); color:rgba(148,163,184,.8); }
        .plan-pro      { background:rgba(14,165,233,.15); color:#38BDF8; }
        .plan-business { background:rgba(168,85,247,.15); color:#c084fc; }
        .status-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.15rem .6rem; border-radius:999px; font-size:.7rem; font-weight:600; }
        .status-active   { background:rgba(34,197,94,.12); color:#4ade80; }
        .status-inactive { background:rgba(239,68,68,.12); color:#f87171; }
        .status-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
        .dot-active   { background:#4ade80; }
        .dot-inactive { background:#f87171; }
        .btn-action { font-size:.75rem; padding:.25rem .75rem; border-radius:.4rem; cursor:pointer; transition:background .2s; border:1px solid; }
        .btn-toggle-on  { background:rgba(239,68,68,.12); border-color:rgba(239,68,68,.25); color:#f87171; }
        .btn-toggle-on:hover  { background:rgba(239,68,68,.22); }
        .btn-toggle-off { background:rgba(34,197,94,.12); border-color:rgba(34,197,94,.25); color:#4ade80; }
        .btn-toggle-off:hover { background:rgba(34,197,94,.22); }
        .btn-impersonate { background:rgba(168,85,247,.12); border-color:rgba(168,85,247,.3); color:#c084fc; }
        .btn-impersonate:hover { background:rgba(168,85,247,.22); }
        .btn-delete { background:rgba(239,68,68,.1); border-color:rgba(239,68,68,.25); color:#f87171; }
        .btn-delete:hover { background:rgba(239,68,68,.25); }
        .table-wrapper { background:rgba(13,25,41,.7); border:1px solid rgba(14,165,233,.1); border-radius:14px; overflow:hidden; }
    </style>
</head>
<body>

<div class="sa-topbar">
    <div class="sa-brand">
        <svg width="26" height="26" viewBox="0 0 32 32" fill="none">
            <rect width="32" height="32" rx="7" fill="#0D1929"/>
            <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
            <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
        </svg>
        INVEXA
        <span class="sa-badge">Super Admin</span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span style="font-size:.8rem; color:rgba(148,163,184,.6);">{{ Auth::user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button class="btn btn-sm" style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.2); color:#f87171; font-size:.78rem;">
                <i class="bi bi-box-arrow-right me-1"></i>Sair
            </button>
        </form>
    </div>
</div>

<div class="container-fluid px-4 py-4" style="max-width:1400px;">

    @if(session('success'))
        <div class="alert mb-4" style="background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.2); color:#4ade80; border-radius:10px;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert mb-4" style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.2); color:#f87171; border-radius:10px;">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="mb-4">
        <h1 style="font-size:1.4rem; font-weight:800; color:var(--ice); margin:0;">Painel de Controle</h1>
        <p style="color:rgba(148,163,184,.55); font-size:.82rem; margin:.2rem 0 0;">Visão geral do SaaS — {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- MÉTRICAS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">MRR Estimado</div>
                        <div class="metric-value">R$&nbsp;{{ number_format($mrr, 0, ',', '.') }}</div>
                        <div class="metric-sub">receita mensal recorrente</div>
                    </div>
                    <i class="bi bi-currency-dollar metric-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">Total de Empresas</div>
                        <div class="metric-value">{{ $totalCompanies }}</div>
                        <div class="metric-sub">{{ $activeCompanies }} ativas</div>
                    </div>
                    <i class="bi bi-buildings metric-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">Novas este mês</div>
                        <div class="metric-value">{{ $newThisMonth }}</div>
                        <div class="metric-sub">{{ now()->translatedFormat('F Y') }}</div>
                    </div>
                    <i class="bi bi-graph-up-arrow metric-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">Churn este mês</div>
                        <div class="metric-value">{{ $churnThisMonth }}</div>
                        <div class="metric-sub">desativadas no período</div>
                    </div>
                    <i class="bi bi-person-dash metric-icon"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- PLANOS --}}
    @php
        $total = array_sum($planCounts) ?: 1;
        $planColors = ['free' => '#94a3b8', 'pro' => '#38BDF8', 'business' => '#c084fc'];
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-4">
            <div class="metric-card h-100">
                <div class="sa-section-title">Distribuição de Planos</div>
                @foreach(['free','pro','business'] as $plan)
                    @php $count = $planCounts[$plan] ?? 0; $pct = round($count/$total*100); @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                            <span style="color:rgba(226,232,240,.75); text-transform:capitalize;">{{ $plan }}</span>
                            <span style="color:rgba(148,163,184,.55);">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div style="height:6px; background:rgba(255,255,255,.06); border-radius:999px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $planColors[$plan] }}; border-radius:999px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="metric-card h-100">
                <div class="sa-section-title">Resumo Rápido</div>
                <div class="row g-3">
                    <div class="col-6"><div style="font-size:.75rem; color:rgba(148,163,184,.55);">Empresas Ativas</div><div style="font-size:1.5rem; font-weight:800; color:#4ade80;">{{ $activeCompanies }}</div></div>
                    <div class="col-6"><div style="font-size:.75rem; color:rgba(148,163,184,.55);">Empresas Inativas</div><div style="font-size:1.5rem; font-weight:800; color:#f87171;">{{ $totalCompanies - $activeCompanies }}</div></div>
                    <div class="col-6"><div style="font-size:.75rem; color:rgba(148,163,184,.55);">Plano Pro</div><div style="font-size:1.5rem; font-weight:800; color:#38BDF8;">{{ $planCounts['pro'] ?? 0 }}</div></div>
                    <div class="col-6"><div style="font-size:.75rem; color:rgba(148,163,184,.55);">Plano Business</div><div style="font-size:1.5rem; font-weight:800; color:#c084fc;">{{ $planCounts['business'] ?? 0 }}</div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA DE EMPRESAS --}}
    <div class="sa-section-title">Empresas Cadastradas</div>
    <div class="table-wrapper mb-4">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Empresa</th>
                    <th>Plano</th>
                    <th>Status</th>
                    <th>Usuários</th>
                    <th>Trial até</th>
                    <th>Criada em</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr>
                    <td style="color:rgba(148,163,184,.4); font-size:.75rem;">{{ $company->id }}</td>
                    <td>
                        <div style="font-weight:600; color:var(--ice);">{{ $company->name }}</div>
                        @if($company->email)
                            <div style="font-size:.72rem; color:rgba(148,163,184,.5);">{{ $company->email }}</div>
                        @endif
                    </td>
                    <td><span class="plan-badge plan-{{ $company->plan }}">{{ $company->plan }}</span></td>
                    <td>
                        <span class="status-badge {{ $company->active ? 'status-active' : 'status-inactive' }}">
                            <span class="status-dot {{ $company->active ? 'dot-active' : 'dot-inactive' }}"></span>
                            {{ $company->active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </td>
                    <td style="text-align:center;">{{ $company->users_count }}</td>
                    <td style="font-size:.78rem; color:rgba(148,163,184,.6);">
                        @if($company->trial_ends_at)
                            {{ \Carbon\Carbon::parse($company->trial_ends_at)->format('d/m/Y') }}
                        @else
                            <span style="opacity:.4;">—</span>
                        @endif
                    </td>
                    <td style="font-size:.78rem; color:rgba(148,163,184,.6);">{{ $company->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            {{-- Entrar como --}}
                            <form action="{{ route('admin.companies.impersonate', $company) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn-action btn-impersonate" title="Entrar como admin desta empresa">
                                    <i class="bi bi-person-badge me-1"></i>Suporte
                                </button>
                            </form>
                            {{-- Ativar/Desativar --}}
                            <form action="{{ route('admin.companies.toggle', $company) }}" method="POST" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action {{ $company->active ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                                    {{ $company->active ? 'Desativar' : 'Ativar' }}
                                </button>
                            </form>
                            {{-- Excluir --}}
                            <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="m-0"
                                  onsubmit="return confirm('Excluir a empresa &quot;{{ addslashes($company->name) }}&quot; e todos os seus usuários? Esta ação é irreversível.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Excluir empresa">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center; padding:2rem; color:rgba(148,163,184,.4);">Nenhuma empresa cadastrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($companies->hasPages())
        <div class="d-flex justify-content-center">{{ $companies->links() }}</div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
