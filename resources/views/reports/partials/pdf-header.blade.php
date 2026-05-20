@php
    $co    = auth()->user()?->company;
    $name  = $co?->name  ?? 'Invexa';
    $cnpj  = $co?->cnpj  ?? '';
    $addr  = $co?->address ?? '';
    $phone = $co?->phone  ?? '';
    $email = $co?->email  ?? '';
    // Converte o path do logo para base64 para funcionar no DomPDF/Snappy
    $logoBase64 = null;
    if ($co?->logo) {
        $logoPath = storage_path('app/public/' . $co->logo);
        if (file_exists($logoPath)) {
            $mime = mime_content_type($logoPath);
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }
@endphp
<table width="100%" style="margin-bottom:18px;border-bottom:2px solid #1d4ed8;padding-bottom:12px;">
  <tr>
    <td style="width:80px;vertical-align:middle;">
      @if($logoBase64)
        <img src="{{ $logoBase64 }}" alt="Logo" style="height:60px;width:auto;max-width:110px;object-fit:contain;">
      @else
        <div style="width:52px;height:52px;background:#0D1929;border-radius:8px;display:flex;align-items:center;justify-content:center;">
          <svg width="34" height="34" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="7" fill="#0D1929"/><path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/><circle cx="24" cy="10" r="2.2" fill="#38BDF8"/></svg>
        </div>
      @endif
    </td>
    <td style="padding-left:14px;vertical-align:middle;">
      <div style="font-size:16px;font-weight:700;color:#0D1929;">{{ $name }}</div>
      @if($cnpj)  <div style="font-size:11px;color:#555;">CNPJ: {{ $cnpj }}</div>@endif
      @if($addr)  <div style="font-size:11px;color:#555;">{{ $addr }}</div>@endif
      @if($phone) <div style="font-size:11px;color:#555;">Tel: {{ $phone }}</div>@endif
      @if($email) <div style="font-size:11px;color:#555;">{{ $email }}</div>@endif
    </td>
    <td style="text-align:right;vertical-align:middle;">
      <div style="font-size:13px;font-weight:700;color:#1d4ed8;">{{ $reportTitle ?? 'Relatório' }}</div>
      <div style="font-size:11px;color:#888;">Gerado em: {{ now()->format('d/m/Y H:i') }}</div>
    </td>
  </tr>
</table>
