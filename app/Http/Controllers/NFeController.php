<?php

namespace App\Http\Controllers;

use App\Models\Nfe;
use App\Models\Sale;
use App\Services\FocusNfeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NFeController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Listagem
    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $nfes = Nfe::with(['sale', 'customer'])
            ->latest()
            ->paginate(20);

        return view('nfes.index', compact('nfes'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Detalhe
    // ─────────────────────────────────────────────────────────────────────────
    public function show(Nfe $nfe): View
    {
        $nfe->load(['sale.items.product', 'customer', 'user']);
        return view('nfes.show', compact('nfe'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Emitir NF-e a partir de uma venda
    // ─────────────────────────────────────────────────────────────────────────
    public function emitir(Sale $sale): RedirectResponse
    {
        // Garante que todas as relações necessárias estão carregadas do banco
        $sale->load(['customer', 'items.product', 'company']);

        $company = auth()->user()->company;

        if (empty($company->focusnfe_token)) {
            return back()->with('error', 'Configure o token da Focus NF-e em Configurações → Fiscal antes de emitir.');
        }

        // Impede dupla emissão
        $jaEmitida = $sale->nfes()->whereIn('status', [
            Nfe::STATUS_AUTORIZADA,
            Nfe::STATUS_PROCESSANDO,
        ])->exists();

        if ($jaEmitida) {
            return back()->with('error', 'Já existe uma NF-e autorizada ou em processamento para esta venda.');
        }

        $service = new FocusNfeService($company);
        $result  = $service->emitir($sale);

        if (!$result['ok']) {
            return back()->with('error', 'Erro ao enviar para a Focus NF-e: ' . json_encode($result['response']));
        }

        $retorno = $result['response'];

        // Determina status inicial
        $statusFocus = $retorno['status'] ?? null;
        $statusMap   = [
            'autorizado'  => Nfe::STATUS_AUTORIZADA,
            'processando' => Nfe::STATUS_PROCESSANDO,
            'denegado'    => Nfe::STATUS_DENEGADA,
            'erro'        => Nfe::STATUS_REJEITADA,
        ];
        $status = $statusMap[$statusFocus] ?? Nfe::STATUS_PROCESSANDO;

        $nfe = Nfe::create([
            'company_id'       => $company->id,
            'sale_id'          => $sale->id,
            'customer_id'      => $sale->customer_id,
            'user_id'          => auth()->id(),
            'numero'           => $retorno['numero_nfe'] ?? null,
            'serie'            => $company->nfe_serie ?? '1',
            'status'           => $status,
            'ambiente'         => $company->focusnfe_ambiente ?? 'homologacao',
            'ref_focusnfe'     => $result['ref'],
            'data_emissao'     => now(),
            'valor_total'      => $sale->total,
            'valor_produtos'   => $sale->items->sum(fn($i) => $i->quantity * $i->unit_price),
            'payload_enviado'  => $result['payload'],
            'retorno_focusnfe' => $retorno,
            'chave_acesso'     => $retorno['chave_nfe'] ?? null,
            'protocolo'        => $retorno['protocolo'] ?? null,
            'motivo_rejeicao'  => $status === Nfe::STATUS_REJEITADA
                ? ($retorno['mensagem_sefaz'] ?? $retorno['erros'][0]['mensagem'] ?? null)
                : null,
        ]);

        if ($status === Nfe::STATUS_AUTORIZADA) {
            $nfe->update(['data_autorizacao' => now()]);
            return redirect()->route('nfes.show', $nfe)->with('success', 'NF-e autorizada com sucesso!');
        }

        return redirect()->route('nfes.show', $nfe)
            ->with('info', 'NF-e enviada para processamento. Consulte o status em instantes.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Consultar status na SEFAZ via Focus
    // ─────────────────────────────────────────────────────────────────────────
    public function consultar(Nfe $nfe): RedirectResponse
    {
        $company = auth()->user()->company;
        $service = new FocusNfeService($company);

        $nfe = $service->syncStatus($nfe);

        return back()->with('success', 'Status atualizado: ' . $nfe->status_label);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cancelar NF-e
    // ─────────────────────────────────────────────────────────────────────────
    public function cancelar(Request $request, Nfe $nfe): RedirectResponse
    {
        $request->validate([
            'justificativa' => 'required|string|min:15|max:255',
        ]);

        if (!$nfe->isAutorizada()) {
            return back()->with('error', 'Apenas NF-es autorizadas podem ser canceladas.');
        }

        $company = auth()->user()->company;
        $service = new FocusNfeService($company);
        $result  = $service->cancelar($nfe->ref_focusnfe, $request->justificativa);

        if (($result['status'] ?? 0) >= 400) {
            return back()->with('error', 'Erro ao cancelar: ' . json_encode($result['response']));
        }

        $nfe->update([
            'status'             => Nfe::STATUS_CANCELADA,
            'data_cancelamento'  => now(),
            'retorno_focusnfe'   => $result['response'],
        ]);

        return back()->with('success', 'NF-e cancelada com sucesso.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Carta de Correção Eletrônica
    // ─────────────────────────────────────────────────────────────────────────
    public function cartaCorrecao(Request $request, Nfe $nfe): RedirectResponse
    {
        $request->validate([
            'correcao' => 'required|string|min:15|max:1000',
        ]);

        if (!$nfe->isAutorizada()) {
            return back()->with('error', 'Apenas NF-es autorizadas podem receber CC-e.');
        }

        $company = auth()->user()->company;
        $service = new FocusNfeService($company);
        $result  = $service->cartaCorrecao($nfe->ref_focusnfe, $request->correcao);

        if (($result['status'] ?? 0) >= 400) {
            return back()->with('error', 'Erro ao enviar CC-e: ' . json_encode($result['response']));
        }

        $nfe->update([
            'cce_correcao'  => $request->correcao,
            'cce_protocolo' => $result['response']['protocolo'] ?? null,
            'cce_data'      => now(),
        ]);

        return back()->with('success', 'Carta de Correção enviada com sucesso.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Download XML
    // ─────────────────────────────────────────────────────────────────────────
    public function downloadXml(Nfe $nfe)
    {
        $company = auth()->user()->company;
        $service = new FocusNfeService($company);
        $result  = $service->consultar($nfe->ref_focusnfe . '?completo=true');

        $xmlUrl = $result['response']['caminho_xml_nota_fiscal'] ?? null;
        if (!$xmlUrl) {
            return back()->with('error', 'XML não disponível ainda.');
        }

        return redirect($xmlUrl);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Download DANFE
    // ─────────────────────────────────────────────────────────────────────────
    public function downloadDanfe(Nfe $nfe)
    {
        $company = auth()->user()->company;
        $service = new FocusNfeService($company);
        $result  = $service->consultar($nfe->ref_focusnfe . '?completo=true');

        $pdfUrl = $result['response']['caminho_danfe'] ?? null;
        if (!$pdfUrl) {
            return back()->with('error', 'DANFE não disponível ainda.');
        }

        return redirect($pdfUrl);
    }
}
