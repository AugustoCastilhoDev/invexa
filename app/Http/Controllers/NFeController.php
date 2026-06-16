<?php

namespace App\Http\Controllers;

use App\Models\Nfe;
use App\Models\Sale;
use App\Services\NFeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NFeController extends Controller
{
    // ── Listagem ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $nfes = Nfe::with(['sale', 'customer', 'user'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('numero', 'like', "%{$request->search}%")
                ->orWhere('chave_acesso', 'like', "%{$request->search}%"))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('nfes.index', compact('nfes'));
    }

    public function show(Nfe $nfe)
    {
        $nfe->load(['sale.items.product', 'customer', 'user', 'company']);
        return view('nfes.show', compact('nfe'));
    }

    // ── Emissão ──────────────────────────────────────────────────────────────

    public function emitir(Request $request, Sale $sale)
    {
        $company = auth()->user()->company;

        if (! $company->focusnfe_token) {
            return back()->with('error', 'Configure o token do Focus NFe nas configurações fiscais da empresa.');
        }

        // Verifica se já existe NF-e autorizada para essa venda
        $existing = Nfe::where('sale_id', $sale->id)
            ->where('status', Nfe::STATUS_AUTORIZADA)
            ->first();

        if ($existing) {
            return back()->with('error', 'Já existe uma NF-e autorizada para esta venda (Nº ' . $existing->numero_formatado . ').');
        }

        $service = new NFeService($company);
        $nfe     = $service->emitir($sale, auth()->user());

        $msg = match ($nfe->status) {
            Nfe::STATUS_AUTORIZADA  => 'NF-e emitida e autorizada com sucesso!',
            Nfe::STATUS_PROCESSANDO => 'NF-e enviada ao SEFAZ, aguardando autorização.',
            default                 => 'NF-e rejeitada: ' . $nfe->motivo_rejeicao,
        };

        $tipo = $nfe->isAutorizada() ? 'success' : ($nfe->isPendente() ? 'warning' : 'error');

        return redirect()->route('nfes.show', $nfe)->with($tipo, $msg);
    }

    // ── Consulta de status ────────────────────────────────────────────────────

    public function consultar(Nfe $nfe)
    {
        $service = new NFeService(auth()->user()->company);
        $nfe     = $service->consultar($nfe);

        return back()->with('success', 'Status atualizado: ' . $nfe->status_label);
    }

    // ── Cancelamento ──────────────────────────────────────────────────────────

    public function cancelar(Request $request, Nfe $nfe)
    {
        $request->validate([
            'justificativa' => 'required|string|min:15|max:255',
        ]);

        try {
            $service = new NFeService(auth()->user()->company);
            $service->cancelar($nfe, $request->justificativa);
            return back()->with('success', 'NF-e cancelada com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ── Carta de Correção ───────────────────────────────────────────────────

    public function cartaCorrecao(Request $request, Nfe $nfe)
    {
        $request->validate([
            'correcao' => 'required|string|min:15|max:1000',
        ]);

        try {
            $service = new NFeService(auth()->user()->company);
            $service->cartaCorrecao($nfe, $request->correcao);
            return back()->with('success', 'Carta de Correção enviada com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ── Downloads ─────────────────────────────────────────────────────────────

    public function downloadXml(Nfe $nfe)
    {
        $service = new NFeService(auth()->user()->company);

        try {
            $path = $nfe->xml_path ?? $service->downloadXml($nfe);
            return Storage::disk('local')->download($path, "NF-e_{$nfe->numero_formatado}.xml");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadDanfe(Nfe $nfe)
    {
        $service = new NFeService(auth()->user()->company);

        try {
            $path = $nfe->danfe_path ?? $service->downloadDanfe($nfe);
            return Storage::disk('local')->download($path, "DANFE_{$nfe->numero_formatado}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
