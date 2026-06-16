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

    public function __construct(Company $company)
    {
        // Nomes corretos conforme tabela companies e FiscalSettingsController
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
            'ok'       => $response->successful() || $response->status() === 422,
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

        // Destinatário
        $dest = [
            'nome' => $customer?->name ?? $sale->customer_name ?? 'CONSUMIDOR',
        ];
        if ($customer?->cpf_cnpj) {
            $doc = preg_replace('/\D/', '', $customer->cpf_cnpj);
            if (strlen($doc) === 14) {
                $dest['cnpj'] = $doc;
            } elseif (strlen($doc) === 11) {
                $dest['cpf'] = $doc;
            }
        }
        if ($customer?->email) {
            $dest['email'] = $customer->email;
        }
        if (!empty($customer->address)) {
            $addr = is_array($customer->address) ? $customer->address : [];
            $dest['logradouro']  = $addr['street']       ?? '';
            $dest['numero']      = $addr['number']       ?? 'S/N';
            $dest['complemento'] = $addr['complement']   ?? '';
            $dest['bairro']      = $addr['neighborhood'] ?? '';
            $dest['municipio']   = $addr['city']         ?? '';
            $dest['uf']          = $addr['state']        ?? '';
            $dest['cep']         = preg_replace('/\D/', '', $addr['zip'] ?? '');
            $dest['pais']        = 'Brasil';
            $dest['codigo_pais'] = '1058';
        }
        if (!empty($customer?->phone)) {
            $dest['telefone'] = preg_replace('/\D/', '', $customer->phone);
        }

        // Itens
        $itens = [];
        foreach ($items as $i => $item) {
            $product = $item->product;
            $itens[] = [
                'numero_item'               => $i + 1,
                'codigo_produto'            => (string) ($product->sku ?? $product->id),
                'descricao'                 => $product->name,
                'codigo_ncm'                => preg_replace('/\D/', '', $product->ncm ?? '00000000'),
                'cfop'                      => $product->cfop ?? '5102',
                'unidade_comercial'         => $product->unit ?? 'UN',
                'quantidade_comercial'      => (float) $item->quantity,
                'valor_unitario_comercial'  => (float) $item->unit_price,
                'valor_bruto'               => (float) ($item->quantity * $item->unit_price),
                'unidade_tributavel'        => $product->unit ?? 'UN',
                'quantidade_tributavel'     => (float) $item->quantity,
                'valor_unitario_tributavel' => (float) $item->unit_price,
                'codigo_barras_comercial'   => $product->barcode ?? 'SEM GTIN',
                'inclui_no_total'           => 1,
                'icms_modalidade'           => 102,
                'icms_csosn'                => '102',
                'pis_modalidade'            => 'NT',
                'cofins_modalidade'         => 'NT',
            ];
        }

        return [
            'natureza_operacao'  => 'Venda de mercadoria',
            'forma_pagamento'    => 0,
            'tipo_documento'     => 1,
            'local_destino'      => 1,
            'finalidade_emissao' => 1,
            'consumidor_final'   => 1,
            'presenca_comprador' => 1,
            'modalidade_frete'   => 9,
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
