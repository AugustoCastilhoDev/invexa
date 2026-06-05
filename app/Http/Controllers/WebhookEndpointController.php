<?php

namespace App\Http\Controllers;

use App\Models\WebhookEndpoint;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookEndpointController extends Controller
{
    private function authorizeBusinessPlan(): void
    {
        if (auth()->user()->company->plan !== 'business') {
            abort(403, 'Webhooks disponíveis apenas no plano Business.');
        }
    }

    public function index()
    {
        $company    = auth()->user()->company;
        $isBusiness = $company->plan === 'business';
        $canCreate  = $isBusiness;

        $webhooks = $isBusiness
            ? WebhookEndpoint::where('company_id', $company->id)->latest()->get()
            : collect();

        return view('webhooks.index', compact('webhooks', 'isBusiness', 'canCreate'));
    }

    public function create()
    {
        $this->authorizeBusinessPlan();
        $events = $this->availableEvents();
        return view('webhooks.form', compact('events'));
    }

    public function store(Request $request)
    {
        $this->authorizeBusinessPlan();
        $data = $request->validate([
            'url'         => 'required|url|max:500',
            'description' => 'nullable|string|max:255',
            'events'      => 'required|array|min:1',
            'events.*'    => 'string',
            'active'      => 'boolean',
        ]);

        $company = auth()->user()->company;
        WebhookEndpoint::create([
            'company_id'  => $company->id,
            'url'         => $data['url'],
            'description' => $data['description'] ?? null,
            'events'      => $data['events'],
            'secret'      => WebhookEndpoint::generateSecret(),
            'active'      => true,
        ]);

        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook criado com sucesso.');
    }

    public function show(WebhookEndpoint $webhook)
    {
        $this->authorizeBusinessPlan();
        $this->authorizeEndpoint($webhook);
        return view('webhooks.show', compact('webhook'));
    }

    public function edit(WebhookEndpoint $webhook)
    {
        $this->authorizeBusinessPlan();
        $this->authorizeEndpoint($webhook);
        $events = $this->availableEvents();
        return view('webhooks.form', compact('webhook', 'events'));
    }

    public function update(Request $request, WebhookEndpoint $webhook)
    {
        $this->authorizeBusinessPlan();
        $this->authorizeEndpoint($webhook);
        $data = $request->validate([
            'url'         => 'required|url|max:500',
            'description' => 'nullable|string|max:255',
            'events'      => 'required|array|min:1',
            'events.*'    => 'string',
            'active'      => 'boolean',
        ]);

        $webhook->update([
            'url'         => $data['url'],
            'description' => $data['description'] ?? null,
            'events'      => $data['events'],
            'active'      => $data['active'] ?? true,
        ]);

        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook atualizado com sucesso.');
    }

    public function destroy(WebhookEndpoint $webhook)
    {
        $this->authorizeBusinessPlan();
        $this->authorizeEndpoint($webhook);
        $webhook->delete();
        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook removido.');
    }

    public function regenerateSecret(WebhookEndpoint $webhook)
    {
        $this->authorizeBusinessPlan();
        $this->authorizeEndpoint($webhook);
        $webhook->update(['secret' => WebhookEndpoint::generateSecret()]);
        return redirect()->route('webhooks.show', $webhook)
            ->with('success', 'Secret regenerado com sucesso.');
    }

    /**
     * Dispara um POST de teste diretamente no endpoint,
     * sem passar pelo filtro listensTo() do WebhookDispatcher.
     */
    public function test(WebhookEndpoint $webhook)
    {
        $this->authorizeBusinessPlan();
        $this->authorizeEndpoint($webhook);

        $body = json_encode([
            'event'     => 'webhook.test',
            'timestamp' => now()->toIso8601String(),
            'data'      => [
                'test'    => true,
                'message' => 'Payload de teste do Invexa',
            ],
        ]);

        $signature = hash_hmac('sha256', $body, $webhook->secret);

        try {
            $response = Http::withHeaders([
                'Content-Type'       => 'application/json',
                'X-Invexa-Signature' => 'sha256=' . $signature,
                'X-Invexa-Event'     => 'webhook.test',
            ])
            ->timeout(8)
            ->post($webhook->url, json_decode($body, true));

            $status = $response->status();
            return back()->with('success', "Teste enviado com sucesso para {$webhook->url} — HTTP {$status}.");
        } catch (\Exception $e) {
            Log::warning("Webhook test failed for endpoint {$webhook->id}: " . $e->getMessage());
            return back()->with('error', 'Falha ao enviar teste: ' . $e->getMessage());
        }
    }

    private function authorizeEndpoint(WebhookEndpoint $webhook): void
    {
        if ($webhook->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }

    private function availableEvents(): array
    {
        return [
            'sale.created'            => 'Venda criada',
            'sale.cancelled'          => 'Venda cancelada',
            'sale.deleted'            => 'Venda excluída',
            'product.low_stock'       => 'Estoque abaixo do mínimo',
            'product.created'         => 'Produto criado',
            'product.updated'         => 'Produto atualizado',
            'customer.created'        => 'Cliente criado',
            'bill.paid'               => 'Conta paga',
            'receivable.received'     => 'Recebível recebido',
            'purchase_order.received' => 'Ordem de compra recebida',
        ];
    }
}
