<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Receivable::with('customer')->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', $search));
            });
        }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('from'))   { $query->whereDate('due_date', '>=', $request->from); }
        if ($request->filled('to'))     { $query->whereDate('due_date', '<=', $request->to); }

        $totalPending  = (clone $query)->where('status', 'pendente')->sum('amount');
        $totalReceived = (clone $query)->where('status', 'recebida')->sum('amount_received');
        $countOverdue  = (clone $query)->where('status', 'pendente')->whereDate('due_date', '<', now())->count();

        $receivables = $query->orderBy('due_date')->paginate(15);

        return view('receivables.index', compact('receivables', 'totalPending', 'totalReceived', 'countOverdue'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $customers = \App\Models\Customer::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);
        return view('receivables.form', compact('customers'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'description' => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'amount'      => 'required|numeric|min:0.01',
            'due_date'    => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        Receivable::create(array_merge($data, [
            'company_id' => $companyId,
            'status'     => 'pendente',
        ]));

        return redirect()->route('receivables.index')->with('success', 'Recebível criado com sucesso.');
    }

    public function show(Receivable $receivable)
    {
        $this->authorizeReceivable($receivable);
        return view('receivables.show', compact('receivable'));
    }

    public function edit(Receivable $receivable)
    {
        $this->authorizeReceivable($receivable);
        $companyId = auth()->user()->company_id;
        $customers = \App\Models\Customer::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);
        return view('receivables.form', compact('receivable', 'customers'));
    }

    public function update(Request $request, Receivable $receivable)
    {
        $this->authorizeReceivable($receivable);

        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'description' => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'amount'      => 'required|numeric|min:0.01',
            'due_date'    => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        $receivable->update($data);

        return redirect()->route('receivables.index')->with('success', 'Recebível atualizado com sucesso.');
    }

    public function markAsReceived(Request $request, Receivable $receivable)
    {
        $this->authorizeReceivable($receivable);
        $company = auth()->user()->company;

        $data = $request->validate([
            'amount_received' => 'required|numeric|min:0.01',
            'received_at'     => 'required|date',
        ]);

        $receivable->update([
            'amount_received' => $data['amount_received'],
            'received_at'     => $data['received_at'],
            'status'          => 'recebida',
        ]);

        // Webhook receivable.received
        WebhookDispatcher::dispatch($company, 'receivable.received', [
            'id'              => $receivable->id,
            'description'     => $receivable->description,
            'amount'          => (float) $receivable->amount,
            'amount_received' => (float) $receivable->amount_received,
            'received_at'     => $receivable->received_at,
            'customer_id'     => $receivable->customer_id,
        ]);

        return redirect()->route('receivables.index')->with('success', 'Recebível marcado como recebido.');
    }

    public function destroy(Receivable $receivable)
    {
        $this->authorizeReceivable($receivable);
        $receivable->delete();
        return redirect()->route('receivables.index')->with('success', 'Recebível removido com sucesso.');
    }

    private function authorizeReceivable(Receivable $receivable): void
    {
        if ($receivable->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
