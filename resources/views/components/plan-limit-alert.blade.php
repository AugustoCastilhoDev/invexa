@php
    $company   = auth()->user()?->company;
    $resources = ['products', 'users', 'customers', 'suppliers'];
    $alerts    = [];

    if ($company) {
        foreach ($resources as $resource) {
            $pct = $company->usagePercent($resource);
            if ($pct >= 80) {
                $limit = $company->limit($resource);
                $label = match ($resource) {
                    'products'  => 'Produtos',
                    'users'     => 'Usuários',
                    'customers' => 'Clientes',
                    'suppliers' => 'Fornecedores',
                    default     => $resource,
                };
                $alerts[] = ['label' => $label, 'pct' => $pct, 'limit' => $limit];
            }
        }
    }
@endphp

@if (count($alerts) > 0)
    <div class="d-flex align-items-center gap-3 px-4 py-2"
         style="background:rgba(234,179,8,.12);border-bottom:1px solid rgba(234,179,8,.25);font-size:.85rem;">
        <i class="bi bi-exclamation-triangle-fill" style="color:#facc15;"></i>
        @foreach ($alerts as $alert)
            <span style="color:#fde68a;">
                Limite de <strong>{{ $alert['label'] }}</strong>:
                {{ $alert['pct'] }}% de {{ $alert['limit'] === PHP_INT_MAX ? '∞' : $alert['limit'] }}
            </span>
        @endforeach
        <a href="{{ route('upgrade') }}" class="btn btn-sm ms-auto"
           style="background:rgba(234,179,8,.2);color:#facc15;border:1px solid rgba(234,179,8,.4);font-size:.8rem;">
            <i class="bi bi-arrow-up-circle me-1"></i>Fazer upgrade
        </a>
    </div>
@endif
