@php
    $company = auth()->user()?->company;
    $limits  = $company?->limits() ?? [];
    $alerts  = [];

    if ($company) {
        // Produtos
        if (isset($limits['products']) && $limits['products'] !== -1) {
            $used = $company->products()->count();
            $pct  = $used / $limits['products'] * 100;
            if ($pct >= 80) {
                $alerts[] = [
                    'label' => 'Produtos',
                    'used'  => $used,
                    'limit' => $limits['products'],
                    'pct'   => $pct,
                ];
            }
        }

        // Usuários
        if (isset($limits['users']) && $limits['users'] !== -1) {
            $used = $company->users()->count();
            $pct  = $used / $limits['users'] * 100;
            if ($pct >= 80) {
                $alerts[] = [
                    'label' => 'Usuários',
                    'used'  => $used,
                    'limit' => $limits['users'],
                    'pct'   => $pct,
                ];
            }
        }
    }
@endphp

@if (count($alerts) > 0)
    <div class="alert-plan-limit d-flex align-items-center gap-3 px-4 py-2"
         style="background: rgba(234,179,8,.12); border-bottom: 1px solid rgba(234,179,8,.25); font-size:.85rem;">
        <span style="color:#facc15;"><i class="bi bi-exclamation-triangle-fill me-1"></i></span>
        @foreach ($alerts as $alert)
            <span style="color:#fde68a;">
                Limite de <strong>{{ $alert['label'] }}</strong>:
                {{ $alert['used'] }}/{{ $alert['limit'] }}
                ({{ number_format($alert['pct'], 0) }}%)
            </span>
        @endforeach
        <a href="{{ route('upgrade') }}" class="btn btn-sm ms-auto"
           style="background:rgba(234,179,8,.2); color:#facc15; border:1px solid rgba(234,179,8,.4); font-size:.8rem;">
            <i class="bi bi-arrow-up-circle me-1"></i>Fazer upgrade
        </a>
    </div>
@endif
