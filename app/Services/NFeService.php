<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Nfe;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NFeService
{
    // Base URLs da API Focus NFe
    const BASE_URL_HOMOLOGACAO = 'https://homologacao.focusnfe.com.br/v2';
    const BASE_URL_PRODUCAO    = 'https://api.focusnfe.com.br/v2';

    private Company $company;
    private string  $baseUrl;
    private string  $token;

    public function __construct(Company $company)
    {
        $this->company = $company;
        $this->token   = $company->focusnfe_token ?? '';
        $this->baseUrl = ($company->focusnfe_ambiente === 'producao')
            ? self::BASE_URL_PRODUCAO
            : self::BASE_URL_HOMOLOGACAO;
    }

    // ── Emissão ──────────────────────────────────────────────────────────────

    /**
     * Emite uma NF-e a partir de uma Venda.
     */
    public function emitir(Sale $sale, User $user): Nfe
    {
        $sale->load(['items.product', 'customer']);

        // Gera referência única para o Focus NFe
        $ref = 'inv_' . $this->company->id . '_' . $sale->id . '_' . now()->format('YmdHis');

        // Incrementa número da NF-e na empresa (atomic)
        $this->company->increment('nfe_numero_atual');
        $this->company->refresh();
        $numero = $this->company->nfe_numero_atual;
        $serie  = $this->company->nfe_serie ?? 1;

        // Monta payload
        $payload = $this->montarPayload($sale, $ref, $numero, $serie);

        // Cria registro no banco
        $nfe = Nfe::create([
            'company_id'      => $this->company->id,
            'sale_id'         => $sale->id,
            'customer_id'     => $sale->customer_id,
            'user_id'         => $user->id,
            'serie'           => $serie,
            'numero'          => $numero,
            'status'          => Nfe::STATUS_PROCESSANDO,
            'ambiente'        => $this->company->focusnfe_ambiente ?? Nfe::AMBIENTE_HOMOLOGACAO,
            'ref_focusnfe'    => $ref,
            'data_emissao'    => now(),
            'valor_produtos'  => $sale->items->sum(fn($i) => $i->quantity * $i->unit_price),
            'valor_desconto'  => $sale->discount ?? 0,
            'valor_frete'     => 0,
            'valor_total'     => $sale->total,
            'payload_enviado' => $payload,
        ]);

        // Envia ao Focus NFe
        try {
            $response = Http::withBasicAuth($this->token, '')
                ->timeout(30)
                ->post("{$this->baseUrl}/nfe?ref={$ref}", $payload);

            $body = $response->json();

            $nfe->update([
                'retorno_focusnfe' => $body,
                'status'           => $this->resolverStatus($response->status(), $body),
                'chave_acesso'     => $body['chave_nfe'] ?? null,
                'protocolo'        => $body['protocolo'] ?? null,
                'motivo_rejeicao'  => $body['mensagem_sefaz'] ?? ($body['erros'][0]['mensagem'] ?? null),
                'data_autorizacao' => isset($body['chave_nfe']) ? now() : null,
            ]);

        } catch (\Exception $e) {
            Log::error('[NFeService] Erro ao emitir NF-e', [
                'nfe_id' => $nfe->id,
                'error'  => $e->getMessage(),
            ]);
            $nfe->update(['status' => Nfe::STATUS_REJEITADA, 'motivo_rejeicao' => $e->getMessage()]);
        }

        return $nfe->fresh();
    }

    /**
     * Consulta o status de uma NF-e no Focus NFe e sincroniza o banco.
     */
    public function consultar(Nfe $nfe): Nfe
    {
        try {
            $response = Http::withBasicAuth($this->token, '')
                ->timeout(15)
                ->get("{$this->baseUrl}/nfe/{$nfe->ref_focusnfe}");

            $body = $response->json();

            $update = [
                'retorno_focusnfe' => $body,
                'status'           => $this->resolverStatus($response->status(), $body),
                'chave_acesso'     => $body['chave_nfe'] ?? $nfe->chave_acesso,
                'protocolo'        => $body['protocolo'] ?? $nfe->protocolo,
                'motivo_rejeicao'  => $body['mensagem_sefaz'] ?? $nfe->motivo_rejeicao,
            ];

            if (isset($body['chave_nfe']) && ! $nfe->data_autorizacao) {
                $update['data_autorizacao'] = now();
            }

            $nfe->update($update);

        } catch (\Exception $e) {
            Log::error('[NFeService] Erro ao consultar NF-e', ['nfe_id' => $nfe->id, 'error' => $e->getMessage()]);
        }

        return $nfe->fresh();
    }

    /**
     * Cancela uma NF-e autorizada.
     */
    public function cancelar(Nfe $nfe, string $justificativa): Nfe
    {
        if (! $nfe->isAutorizada()) {
            throw new \RuntimeException('Somente NF-e autorizada pode ser cancelada.');
        }

        if (strlen($justificativa) < 15) {
            throw new \RuntimeException('A justificativa deve ter no mínimo 15 caracteres.');
        }

        try {
            $response = Http::withBasicAuth($this->token, '')
                ->timeout(20)
                ->delete("{$this->baseUrl}/nfe/{$nfe->ref_focusnfe}", [
                    'justificativa' => $justificativa,
                ]);

            $body = $response->json();

            if ($response->successful() || ($body['status'] ?? '') === 'cancelado') {
                $nfe->update([
                    'status'            => Nfe::STATUS_CANCELADA,
                    'data_cancelamento' => now(),
                    'retorno_focusnfe'  => $body,
                    'motivo_rejeicao'   => $justificativa,
                ]);
            } else {
                throw new \RuntimeException($body['mensagem_sefaz'] ?? 'Erro ao cancelar no SEFAZ.');
            }

        } catch (\Exception $e) {
            Log::error('[NFeService] Erro ao cancelar NF-e', ['nfe_id' => $nfe->id, 'error' => $e->getMessage()]);
            throw $e;
        }

        return $nfe->fresh();
    }

    /**
     * Emite Carta de Correção (CC-e).
     */
    public function cartaCorrecao(Nfe $nfe, string $correcao): Nfe
    {
        if (! $nfe->isAutorizada()) {
            throw new \RuntimeException('Somente NF-e autorizada pode receber CC-e.');
        }

        if (strlen($correcao) < 15) {
            throw new \RuntimeException('A correção deve ter no mínimo 15 caracteres.');
        }

        $response = Http::withBasicAuth($this->token, '')
            ->timeout(20)
            ->post("{$this->baseUrl}/nfe/{$nfe->ref_focusnfe}/carta_correcao", [
                'correcao' => $correcao,
            ]);

        $body = $response->json();

        $nfe->update([
            'cce_correcao' => $correcao,
            'cce_protocolo' => $body['protocolo'] ?? null,
            'cce_data'      => now(),
            'retorno_focusnfe' => $body,
        ]);

        return $nfe->fresh();
    }

    /**
     * Baixa o XML da NF-e e salva no storage.
     */
    public function downloadXml(Nfe $nfe): string
    {
        $response = Http::withBasicAuth($this->token, '')
            ->timeout(20)
            ->get("{$this->baseUrl}/nfe/{$nfe->ref_focusnfe}/xml");

        if (! $response->successful()) {
            throw new \RuntimeException('Não foi possível baixar o XML da NF-e.');
        }

        $path = "nfes/{$this->company->id}/{$nfe->ref_focusnfe}.xml";
        Storage::disk('local')->put($path, $response->body());

        $nfe->update(['xml_path' => $path]);

        return $path;
    }

    /**
     * Baixa o DANFE (PDF) da NF-e e salva no storage.
     */
    public function downloadDanfe(Nfe $nfe): string
    {
        $response = Http::withBasicAuth($this->token, '')
            ->timeout(30)
            ->get("{$this->baseUrl}/nfe/{$nfe->ref_focusnfe}/danfe");

        if (! $response->successful()) {
            throw new \RuntimeException('Não foi possível baixar o DANFE.');
        }

        $path = "nfes/{$this->company->id}/{$nfe->ref_focusnfe}.pdf";
        Storage::disk('local')->put($path, $response->body());

        $nfe->update(['danfe_path' => $path]);

        return $path;
    }

    // ── Payload builder ───────────────────────────────────────────────────────

    private function montarPayload(Sale $sale, string $ref, int $numero, int $serie): array
    {
        $c       = $this->company;
        $cliente = $sale->customer;

        $payload = [
            'natureza_operacao'  => 'Venda de Mercadoria',
            'data_emissao'       => now()->format('Y-m-d'),
            'data_entrada_saida' => now()->format('Y-m-d'),
            'tipo_documento'     => 1,  // 1 = Saída
            'finalidade_emissao' => 1,  // 1 = Normal
            'consumidor_final'   => 1,
            'presenca_comprador' => 1,  // 1 = Operação presencial

            // Emitente
            'cnpj_emitente'                  => preg_replace('/\D/', '', $c->cnpj ?? ''),
            'nome_emitente'                  => $c->name,
            'nome_fantasia_emitente'         => $c->name,
            'logradouro_emitente'            => $c->logradouro ?? '',
            'numero_emitente'                => $c->numero_endereco ?? 'S/N',
            'complemento_emitente'           => $c->complemento ?? '',
            'bairro_emitente'                => $c->bairro ?? '',
            'municipio_emitente'             => $c->municipio ?? '',
            'uf_emitente'                    => $c->uf ?? '',
            'cep_emitente'                   => preg_replace('/\D/', '', $c->cep ?? ''),
            'codigo_municipio_emitente'      => $c->codigo_municipio ?? '',
            'telefone_emitente'              => preg_replace('/\D/', '', $c->telefone_fiscal ?? ''),
            'ie_emitente'                    => $c->ie ?? '',
            'regime_tributario_emitente'     => $c->crt ?? '1',

            // Destinatário
            'nome_destinatario'              => $cliente?->name ?? 'Consumidor Final',
            'cpf_destinatario'               => $cliente ? preg_replace('/\D/', '', $cliente->document ?? '') : '',
            'logradouro_destinatario'        => $cliente?->logradouro ?? $cliente?->address ?? '',
            'numero_destinatario'            => $cliente?->numero_endereco ?? 'S/N',
            'complemento_destinatario'       => $cliente?->complemento ?? '',
            'bairro_destinatario'            => $cliente?->bairro ?? '',
            'municipio_destinatario'         => $cliente?->municipio ?? $cliente?->city ?? '',
            'uf_destinatario'                => $cliente?->uf ?? $cliente?->state ?? '',
            'cep_destinatario'               => preg_replace('/\D/', '', $cliente?->cep ?? ''),
            'codigo_municipio_destinatario'  => $cliente?->codigo_municipio ?? '',
            'indicador_ie_destinatario'      => $cliente?->indicador_ie ?? '9',

            // Totais
            'valor_desconto'    => number_format($sale->discount ?? 0, 2, '.', ''),
            'valor_frete'       => '0.00',
            'modalidade_frete'  => 9, // 9 = Sem frete

            // Pagamento
            'forma_pagamento' => [[
                'forma_pagamento' => '01', // 01 = Dinheiro (padrão)
                'valor_pagamento' => number_format($sale->total, 2, '.', ''),
            ]],

            'itens' => $this->montarItens($sale),
        ];

        // Remover campos vazios opcionais para não poluir o payload
        return array_filter($payload, fn($v) => $v !== '' && $v !== null);
    }

    private function montarItens(Sale $sale): array
    {
        $itens = [];
        $num   = 1;

        foreach ($sale->items as $item) {
            $produto = $item->product;

            $itens[] = [
                'numero_item'              => $num++,
                'codigo_produto'           => $produto->id,
                'descricao'                => $produto->name,
                'codigo_ncm'               => $produto->ncm ?? '00000000',
                'cfop'                     => $produto->cfop ?? '5102',
                'unidade_comercial'        => $produto->unidade_tributavel ?? 'UN',
                'quantidade_comercial'     => number_format($item->quantity, 4, '.', ''),
                'valor_unitario_comercial' => number_format($item->unit_price, 10, '.', ''),
                'valor_bruto'              => number_format($item->quantity * $item->unit_price, 2, '.', ''),
                'unidade_tributavel'       => $produto->unidade_tributavel ?? 'UN',
                'quantidade_tributavel'    => number_format($item->quantity, 4, '.', ''),
                'valor_unitario_tributavel' => number_format($item->unit_price, 10, '.', ''),
                'codigo_barras_comercial'  => $produto->barcode ?? '',
                'inclui_no_total'          => 1,
                'origem'                   => $produto->origem_produto ?? '0',
                'cst'                      => $produto->cst_icms ?? '400', // CSOSN 400 = Tributado SN
                'csosn'                    => $produto->cst_icms ?? '400',
                'modalidade_base_calculo'  => 3,
                'valor_base_calculo'       => number_format($item->quantity * $item->unit_price, 2, '.', ''),
                'aliquota'                 => number_format($produto->aliquota_icms ?? 0, 2, '.', ''),
                'valor_icms'               => '0.00',
                'cst_pis'                  => $produto->cst_pis ?? '07',
                'valor_base_calculo_pis'   => '0.00',
                'aliquota_pis'             => number_format($produto->aliquota_pis ?? 0, 2, '.', ''),
                'valor_pis'                => '0.00',
                'cst_cofins'               => $produto->cst_cofins ?? '07',
                'valor_base_calculo_cofins' => '0.00',
                'aliquota_cofins'          => number_format($produto->aliquota_cofins ?? 0, 2, '.', ''),
                'valor_cofins'             => '0.00',
            ];
        }

        return $itens;
    }

    // ── Helpers internos ──────────────────────────────────────────────────────

    private function resolverStatus(int $httpStatus, ?array $body): string
    {
        $statusFocus = $body['status'] ?? null;

        return match (true) {
            $statusFocus === 'autorizado'                       => Nfe::STATUS_AUTORIZADA,
            $statusFocus === 'cancelado'                        => Nfe::STATUS_CANCELADA,
            $statusFocus === 'denegado'                         => Nfe::STATUS_DENEGADA,
            in_array($statusFocus, ['processando_autorizacao',
                'requisicao_cancelamento', 'em_digitacao'])    => Nfe::STATUS_PROCESSANDO,
            $httpStatus >= 200 && $httpStatus < 300             => Nfe::STATUS_PROCESSANDO,
            default                                            => Nfe::STATUS_REJEITADA,
        };
    }
}
