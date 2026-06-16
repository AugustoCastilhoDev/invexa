<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Nfe;
use App\Models\Sale;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FocusNfeService
{
    private string $baseUrl;
    private string $token;
    private string $ambiente;

    private const CNPJ_HOMOLOGACAO = '34785515000166';

    public function __construct(Company $company)
    {
        $this->ambiente = $company->focusnfe_ambiente ?? 'homologacao';
        $this->token    = $company->focusnfe_token    ?? '';
        $this->baseUrl  = $this->ambiente === 'producao'
            ? 'https://api.focusnfe.com.br'
            : 'https://homologacao.focusnfe.com.br';
    }

    public function emitir(Sale $sale): array
    {
        $customer = \App\Models\Customer::find($sale->customer_id);

        $ref     = 'INVEXA-' . $sale->id . '-' . Str::random(6);
        $payload = $this->buildPayload($sale, $ref, $customer);

        Log::info('[FocusNFe] payload completo', ['sale_id' => $sale->id, 'payload' => $payload]);

        $response = Http::withBasicAuth($this->token, '')
            ->post("{$this->baseUrl}/v2/nfe?ref={$ref}", $payload);

        Log::info('[FocusNFe] emitir', [
            'sale_id' => $sale->id,
            'ref'     => $ref,
            'status'  => $response->status(),
            'body'    => $response->json(),
        ]);

        return [
            'ref'      => $ref,
            'payload'  => $payload,
            'status'   => $response->status(),
            'response' => $response->json(),
            'ok'       => $response->status() === 202,
        ];
    }

    public function consultar(string $ref): array
    {
        $response = Http::withBasicAuth($this->token, '')
            ->get("{$this->baseUrl}/v2/nfe/{$ref}");

        return [
            'status'   => $response->status(),
            'response' => $response->json(),
        ];
    }

    public function cancelar(string $ref, string $justificativa): array
    {
        $response = Http::withBasicAuth($this->token, '')
            ->delete("{$this->baseUrl}/v2/nfe/{$ref}", [
                'justificativa' => $justificativa,
            ]);

        return [
            'status'   => $response->status(),
            'response' => $response->json(),
        ];
    }

    public function cartaCorrecao(string $ref, string $correcao): array
    {
        $response = Http::withBasicAuth($this->token, '')
            ->post("{$this->baseUrl}/v2/nfe/{$ref}/carta_correcao", [
                'texto_correcao' => $correcao,
            ]);

        return [
            'status'   => $response->status(),
            'response' => $response->json(),
        ];
    }

    private function buildPayload(Sale $sale, string $ref, ?\App\Models\Customer $customer = null): array
    {
        $company = $sale->company ?? \App\Models\Company::find($sale->company_id);

        if ($customer === null) {
            $customer = $sale->customer;
        }

        $items = $sale->items()->with('product')->get();

        $isHomologacao = $this->ambiente !== 'producao';

        $cnpjEmitente = $isHomologacao
            ? self::CNPJ_HOMOLOGACAO
            : preg_replace('/\D/', '', $company->cnpj ?? '');

        // ── Destinatário: campos no nível raiz com sufixo _destinatario ──
        $nomeDestinatario = $isHomologacao
            ? 'NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL'
            : ($customer?->name ?? 'CONSUMIDOR');

        $payload = [
            'nome_destinatario' => $nomeDestinatario,
        ];

        // CPF / CNPJ
        if (!empty($customer?->document)) {
            $doc = preg_replace('/\D/', '', $customer->document);
            if (strlen($doc) === 14) {
                $payload['cnpj_destinatario'] = $doc;
            } elseif (strlen($doc) === 11) {
                $payload['cpf_destinatario'] = $doc;
            }
        }

        if (!empty($customer?->email)) {
            $payload['email_destinatario'] = $customer->email;
        }

        // Endereço
        $logradouro = trim($customer->logradouro      ?? '');
        $numero     = trim($customer->numero_endereco ?? '');
        $bairro     = trim($customer->bairro          ?? '');
        $municipio  = trim($customer->municipio       ?? '');
        $uf         = trim($customer->uf              ?? '');
        $cep        = preg_replace('/\D/', '', $customer->cep ?? '');

        $hasAddr = $logradouro && $municipio && $uf && strlen($cep) === 8;
        $temDoc  = isset($payload['cpf_destinatario']) || isset($payload['cnpj_destinatario']);

        if ($hasAddr) {
            $payload['logradouro_destinatario'] = $logradouro;
            $payload['numero_destinatario']     = $numero ?: 'S/N';
            $payload['bairro_destinatario']     = $bairro ?: 'Centro';
            $payload['municipio_destinatario']  = $municipio;
            $payload['uf_destinatario']         = strtoupper($uf);
            $payload['cep_destinatario']        = $cep;
            $payload['pais_destinatario']       = 'Brasil';
            $payload['codigo_pais_destinatario']= '1058';
            if (!empty($customer->complemento)) {
                $payload['complemento_destinatario'] = $customer->complemento;
            }
        } elseif ($temDoc) {
            // Tem documento mas sem endereço: usa endereço padrão Brasília
            $payload['logradouro_destinatario'] = 'Praca dos Tres Poderes';
            $payload['numero_destinatario']     = 'S/N';
            $payload['bairro_destinatario']     = 'Zona Civico-Administrativa';
            $payload['municipio_destinatario']  = 'Brasilia';
            $payload['uf_destinatario']         = 'DF';
            $payload['cep_destinatario']        = '70150900';
            $payload['pais_destinatario']       = 'Brasil';
            $payload['codigo_pais_destinatario']= '1058';
        }

        if (!empty($customer?->phone)) {
            $phone = preg_replace('/\D/', '', $customer->phone);
            if (strlen($phone) >= 10) {
                $payload['telefone_destinatario'] = $phone;
            }
        }

        // ── Itens ──
        $itens = [];
        foreach ($items as $i => $item) {
            $product    = $item->product;
            $unitPrice  = (float) ($item->price ?? $item->unit_price ?? 0);
            $qty        = (float) $item->quantity;
            $valorBruto = round($qty * $unitPrice, 2);

            $ncm = preg_replace('/\D/', '', $product->ncm ?? '');
            if (strlen($ncm) !== 8) {
                $ncm = '84716049';
            }

            $itens[] = [
                'numero_item'               => $i + 1,
                'codigo_produto'            => (string) ($product->sku ?? $product->id),
                'descricao'                 => $product->name,
                'codigo_ncm'                => $ncm,
                'cfop'                      => $product->cfop ?? '5102',
                'unidade_comercial'         => strtoupper($product->unit ?? 'UN'),
                'quantidade_comercial'      => $qty,
                'valor_unitario_comercial'  => $unitPrice,
                'valor_bruto'               => $valorBruto,
                'unidade_tributavel'        => strtoupper($product->unidade_tributavel ?? $product->unit ?? 'UN'),
                'quantidade_tributavel'     => $qty,
                'valor_unitario_tributavel' => $unitPrice,
                'codigo_barras_comercial'   => $product->barcode ?? 'SEM GTIN',
                'inclui_no_total'           => 1,
                'icms_modalidade'           => 102,
                'icms_csosn'                => '102',
                'pis_modalidade'            => 'NT',
                'cofins_modalidade'         => 'NT',
            ];
        }

        return array_merge([
            'data_emissao'       => now()->format('Y-m-d'),
            'natureza_operacao'  => 'Venda de mercadoria',
            'forma_pagamento'    => 0,
            'tipo_documento'     => 1,
            'local_destino'      => 1,
            'finalidade_emissao' => 1,
            'consumidor_final'   => 1,
            'presenca_comprador' => 1,
            'modalidade_frete'   => 9,
            'cnpj_emitente'      => $cnpjEmitente,
            'items'              => $itens,
        ], $payload);
    }

    public function syncStatus(Nfe $nfe): Nfe
    {
        $result    = $this->consultar($nfe->ref_focusnfe);
        $retorno   = $result['response'] ?? [];
        $statFocus = $retorno['status']  ?? null;

        $statusMap = [
            'autorizado'  => Nfe::STATUS_AUTORIZADA,
            'processando' => Nfe::STATUS_PROCESSANDO,
            'denegado'    => Nfe::STATUS_DENEGADA,
            'cancelado'   => Nfe::STATUS_CANCELADA,
            'erro'        => Nfe::STATUS_REJEITADA,
        ];

        $nfe->status           = $statusMap[$statFocus] ?? Nfe::STATUS_REJEITADA;
        $nfe->retorno_focusnfe = $retorno;
        $nfe->chave_acesso     = $retorno['chave_nfe'] ?? $nfe->chave_acesso;
        $nfe->protocolo        = $retorno['protocolo'] ?? $nfe->protocolo;

        if ($nfe->status === Nfe::STATUS_AUTORIZADA && !$nfe->data_autorizacao) {
            $nfe->data_autorizacao = now();
        }
        if ($nfe->status === Nfe::STATUS_REJEITADA) {
            $nfe->motivo_rejeicao = $retorno['mensagem_sefaz']
                ?? $retorno['erros'][0]['mensagem']
                ?? null;
        }

        $nfe->save();
        return $nfe;
    }
}
