<?php

namespace App\Services;

use App\Models\Company;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDispatcher
{
    /**
     * Dispara o evento para todos os endpoints ativos da empresa.
     *
     * @param Company $company
     * @param string  $event     Ex: 'sale.created'
     * @param array   $payload   Dados do evento
     */
    public static function dispatch(Company $company, string $event, array $payload): void
    {
        if ($company->plan !== 'business') {
            return;
        }

        $endpoints = WebhookEndpoint::where('company_id', $company->id)
            ->where('active', true)
            ->get();

        foreach ($endpoints as $endpoint) {
            if (! $endpoint->listensTo($event)) {
                continue;
            }

            $body = json_encode([
                'event'     => $event,
                'timestamp' => now()->toIso8601String(),
                'data'      => $payload,
            ]);

            $signature = hash_hmac('sha256', $body, $endpoint->secret);

            try {
                Http::withHeaders([
                    'Content-Type'       => 'application/json',
                    'X-Invexa-Signature' => 'sha256=' . $signature,
                    'X-Invexa-Event'     => $event,
                ])
                ->timeout(5)
                ->post($endpoint->url, json_decode($body, true));
            } catch (\Exception $e) {
                Log::warning("Webhook dispatch failed for endpoint {$endpoint->id}: " . $e->getMessage());
            }
        }
    }
}
