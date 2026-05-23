<?php

namespace App\Http\Controllers;

use App\Models\WebhookEndpoint;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;

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

    public function test(WebhookEndpoint $webhook)
    {
        $this->authorizeBusinessPlan();
        $this->authorizeEndpoint($webhook);
        WebhookDispatcher::dispatch(
            auth()->user()->company,
            'webhook.test',
            ['test' => true, 'message' => 'Payload de teste do Invexa']
        );
        return back()->with('success', 'Evento de teste disparado para ' . $webhook->url);
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
