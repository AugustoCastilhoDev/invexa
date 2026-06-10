<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Sale;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PixPaymentService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(Company $company)
    {
        $env = $company->asaas_environment ?? 'production';

        $this->baseUrl = $env === 'sandbox'
            ? 'https://sandbox.asaas.com/api/v3'
            : 'https://api.asaas.com/v3';

        $this->apiKey = $company->asaas_api_key
            ? decrypt($company->asaas_api_key)
            : '';
    }

    /**
     * Gera cobrança Pix para uma venda.
     * Retorna array com charge_id, payload e qrcode_image, ou lança exception.
     */
    public function generateCharge(Sale $sale): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Chave Asaas não configurada para esta empresa.');
        }

        if (! $sale->customer) {
            throw new \RuntimeException('Venda sem cliente vinculado — Pix requer cliente.');
        }

        // Garante ou cria o customer no Asaas
        $asaasCustomerId = $this->ensureAsaasCustomer($sale->customer);

        $dueDate = now()->addDay()->format('Y-m-d');

        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post("{$this->baseUrl}/payments", [
            'customer'        => $asaasCustomerId,
            'billingType'     => 'PIX',
            'value'           => round((float) $sale->total, 2),
            'dueDate'         => $dueDate,
            'description'     => "Venda #{$sale->sale_number} — Invexa",
            'externalReference' => (string) $sale->id,
        ]);

        if ($response->failed()) {
            Log::error('Asaas: falha ao criar cobrança', [
                'sale_id' => $sale->id,
                'status'  => $response->status(),
                'body'    => $response->json(),
            ]);
            throw new \RuntimeException('Erro ao gerar cobrança Pix: ' . ($response->json('errors.0.description') ?? 'erro desconhecido'));
        }

        $chargeId = $response->json('id');

        // Busca QR Code
        $qr = Http::withHeaders(['access_token' => $this->apiKey])
            ->get("{$this->baseUrl}/payments/{$chargeId}/pixQrCode");

        if ($qr->failed()) {
            throw new \RuntimeException('Cobrança criada mas falha ao obter QR Code.');
        }

        return [
            'charge_id'    => $chargeId,
            'payload'      => $qr->json('payload'),
            'qrcode_image' => $qr->json('encodedImage'),
            'expires_at'   => now()->addDay(),
        ];
    }

    /**
     * Busca ou cria o customer no Asaas a partir do Customer do Invexa.
     */
    private function ensureAsaasCustomer(\App\Models\Customer $customer): string
    {
        // Tenta buscar pelo externalReference (cpf/cnpj ou e-mail)
        $search = Http::withHeaders(['access_token' => $this->apiKey])
            ->get("{$this->baseUrl}/customers", [
                'externalReference' => (string) $customer->id,
            ]);

        if ($search->ok() && $search->json('totalCount') > 0) {
            return $search->json('data.0.id');
        }

        // Cria novo customer no Asaas
        $create = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post("{$this->baseUrl}/customers", [
            'name'              => $customer->name,
            'email'             => $customer->email,
            'phone'             => $customer->phone,
            'cpfCnpj'           => $customer->cpf_cnpj ?? null,
            'externalReference' => (string) $customer->id,
        ]);

        if ($create->failed()) {
            throw new \RuntimeException('Erro ao criar customer no Asaas: ' . ($create->json('errors.0.description') ?? 'erro desconhecido'));
        }

        return $create->json('id');
    }

    /**
     * Verifica se a API Key está válida consultando a conta.
     */
    public function testConnection(): bool
    {
        if (empty($this->apiKey)) return false;

        try {
            $response = Http::withHeaders(['access_token' => $this->apiKey])
                ->get("{$this->baseUrl}/myAccount/commercialInfo");
            return $response->ok();
        } catch (\Throwable) {
            return false;
        }
    }
}
