{{-- Cabeçalho reutilizável para todos os PDFs --}}
@php
    $co    = auth()->user()->company;
    $logo  = $co?->logo ? public_path('storage/' . $co->logo) : null;
    $name  = $co?->name  ?? 'Invexa';
    $cnpj  = $co?->cnpj  ?? '';
    $addr  = $co?->address ?? '';
    $phone = $co?->phone  ?? '';
    $email = $co?->email  ?? '';
@endphp
<table width="100%" style="margin-bottom:18px;border-bottom:2px solid #0EA5E9;padding-bottom:12px;">
  <tr>
    <td width="70" style="vertical-align:middle;">
      @if($logo && file_exists($logo))
        <img src="{{ $logo }}" alt="Logo" style="height:56px;width:auto;max-width:100px;object-fit:contain;">
      @else
        <div style="width:56px;height:56px;background:#080D1A;border-radius:8px;display:flex;align-items:center;justify-content:center;">
          {{-- SVG inline --}}
          <svg width="36" height="36" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="7" fill="#080D1A"/><path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/><circle cx="24" cy="10" r="2.2" fill="#38BDF8"/></svg>
        </div>
      @endif
    </td>
    <td style="padding-left:14px;vertical-align:middle;">
      <div style="font-size:17px;font-weight:700;color:#0D1929;">{{ $name }}</div>
      @if($cnpj)  <div style="font-size:11px;color:#64748b;">CNPJ: {{ $cnpj }}</div>@endif
      @if($addr)  <div style="font-size:11px;color:#64748b;">{{ $addr }}</div>@endif
      @if($phone) <div style="font-size:11px;color:#64748b;">Tel: {{ $phone }}</div>@endif
      @if($email) <div style="font-size:11px;color:#64748b;">{{ $email }}</div>@endif
    </td>
    <td style="text-align:right;vertical-align:middle;">
      <div style="font-size:13px;font-weight:600;color:#0EA5E9;">{{ $reportTitle ?? 'Relatório' }}</div>
      <div style="font-size:11px;color:#94a3b8;">Gerado em: {{ now()->format('d/m/Y H:i') }}</div>
    </td>
  </tr>
</table>
