<?php

namespace App\Http\Controllers;

use App\Models\WebhookEndpoint;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WebhookEndpointController extends Controller
{
    private function authorizeBusinessPlan(): void
    {
        $company = auth()->user()->company;
        if ($company->plan !== 'business') {
            abort(403, 'Webhooks disponíveis apenas no plano Business.');
        }
    }

    public function index()
    {
        $this->authorizeBusinessPlan();
        $company   = auth()->user()->company;
        $endpoints = WebhookEndpoint::where('company_id', $company->id)
            ->latest()->get();

        return view('webhooks.index', compact('endpoints'));
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
            'events.*'    => 'in:sale.created,sale.returned,stock.low,customer.created',
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
            'events.*'    => 'in:sale.created,sale.returned,stock.low,customer.created',
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
            'sale.created',
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
            'sale.created'     => 'Venda criada',
            'sale.returned'    => 'Devolução processada',
            'stock.low'        => 'Estoque abaixo do mínimo',
            'customer.created' => 'Novo cliente cadastrado',
        ];
    }
}
