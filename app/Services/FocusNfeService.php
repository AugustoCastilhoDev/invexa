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

    // CNPJ do certificado de testes usado em homologação
    private const CNPJ_HOMOLOGACAO = '34785515000166';

    public function __construct(Company $company)
    {
        $this->ambiente = $company->focusnfe_ambiente ?? 'homologacao';
        $this->token    = $company->focusnfe_token    ?? '';
        $this->baseUrl  = $this->ambiente === 'producao'
            ? 'https://api.focusnfe.com.br'
            : 'https://homologacao.focusnfe.com.br';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Emitir NF-e
    // ─────────────────────────────────────────────────────────────────────────
    public function emitir(Sale $sale): array
    {
        $ref     = 'INVEXA-' . $sale->id . '-' . Str::random(6);
        $payload = $this->buildPayload($sale, $ref);

        $response = Http::withBasicAuth($this->token, '')
            ->post("{$this->baseUrl}/v2/nfe?ref={$ref}", $payload);

        Log::info('[FocusNFe] emitir', [
            'sale_id'  => $sale->id,
            'ref'      => $ref,
            'status'   => $response->status(),
            'body'     => $response->json(),
        ]);

        return [
            'ref'      => $ref,
            'payload'  => $payload,
            'status'   => $response->status(),
            'response' => $response->json(),
            'ok'       => $response->status() === 202,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Consultar status na Focus
    // ─────────────────────────────────────────────────────────────────────────
    public function consultar(string $ref): array
    {
        $response = Http::withBasicAuth($this->token, '')
            ->get("{$this->baseUrl}/v2/nfe/{$ref}");

        return [
            'status'   => $response->status(),
            'response' => $response->json(),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cancelar NF-e
    // ─────────────────────────────────────────────────────────────────────────
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

    // ─────────────────────────────────────────────────────────────────────────
    // Carta de Correção Eletrônica
    // ─────────────────────────────────────────────────────────────────────────
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

    // ─────────────────────────────────────────────────────────────────────────
    // Monta o payload JSON para a Focus NF-e
    // ─────────────────────────────────────────────────────────────────────────
    private function buildPayload(Sale $sale, string $ref): array
    {
        $company  = $sale->company;
        $customer = $sale->customer;
        $items    = $sale->items()->with('product')->get();

        $isHomologacao = $this->ambiente !== 'producao';

        // Destinatário — em homologação a SEFAZ exige nome fixo
        $nomeDestinatario = $isHomologacao
            ? 'NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL'
            : ($customer?->name ?? $sale->customer_name ?? 'CONSUMIDOR');

        $dest = ['nome' => $nomeDestinatario];

        // CPF/CNPJ do destinatário
        $temDocumento = false;
        if ($customer?->cpf_cnpj) {
            $doc = preg_replace('/\D/', '', $customer->cpf_cnpj);
            if (strlen($doc) === 14) {
                $dest['cnpj']   = $doc;
                $temDocumento   = true;
            } elseif (strlen($doc) === 11) {
                $dest['cpf']    = $doc;
                $temDocumento   = true;
            }
        }

        if (!empty($customer?->email)) {
            $dest['email'] = $customer->email;
        }

        // Quando há CPF/CNPJ, a SEFAZ exige endereço completo.
        // Usa dados reais do cliente se disponíveis; caso contrário usa
        // endereço genérico válido (aceito em homologação).
        $addr   = is_array($customer->address ?? null) ? $customer->address : [];
        $street = trim($addr['street']       ?? '');
        $city   = trim($addr['city']         ?? '');
        $state  = trim($addr['state']        ?? '');
        $zip    = preg_replace('/\D/', '', $addr['zip'] ?? '');
        $hasAddr = $street && $city && $state && strlen($zip) === 8;

        if ($temDocumento || $hasAddr) {
            if ($hasAddr) {
                $dest['logradouro'] = $street;
                $dest['numero']     = trim($addr['number'] ?? '') ?: 'S/N';
                $dest['bairro']     = trim($addr['neighborhood'] ?? '') ?: 'Centro';
                $dest['municipio']  = $city;
                $dest['uf']         = strtoupper($state);
                $dest['cep']        = $zip;
                if (!empty($addr['complement'])) {
                    $dest['complemento'] = $addr['complement'];
                }
            } else {
                // Endereço genérico para testes — Brasília/DF
                $dest['logradouro'] = 'Praca dos Tres Poderes';
                $dest['numero']     = 'S/N';
                $dest['bairro']     = 'Zona Cívico-Administrativa';
                $dest['municipio']  = 'Brasilia';
                $dest['uf']         = 'DF';
                $dest['cep']        = '70150900';
            }
            $dest['pais']        = 'Brasil';
            $dest['codigo_pais'] = '1058';
        }

        if (!empty($customer?->phone)) {
            $phone = preg_replace('/\D/', '', $customer->phone);
            if (strlen($phone) >= 10) {
                $dest['telefone'] = $phone;
            }
        }

        // Emitente — em homologação usa CNPJ do certificado de testes
        $cnpjEmitente = $isHomologacao
            ? self::CNPJ_HOMOLOGACAO
            : preg_replace('/\D/', '', $company->cnpj ?? '');

        // Itens — usa $item->price (coluna real do SaleItem)
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

        return [
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
            'destinatario'       => $dest,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Atualiza o model Nfe a partir do retorno da Focus
    // ─────────────────────────────────────────────────────────────────────────
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
