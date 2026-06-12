<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;

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
        if ($request->filled('status'))   { $query->where('status', $request->status); }
        if ($request->filled('category')) { $query->where('category', $request->category); }
        if ($request->filled('from'))     { $query->whereDate('due_date', '>=', $request->from); }
        if ($request->filled('to'))       { $query->whereDate('due_date', '<=', $request->to); }

        // KPIs (base completa da empresa, sem filtros de busca)
        $base = Receivable::where('company_id', $companyId);

        $totalPending  = (clone $base)->where('status', 'pendente')->sum('amount');
        $totalReceived = (clone $base)->where('status', 'recebida')->sum('amount_received');
        $totalOverdue  = (clone $base)->where('status', 'pendente')->whereDate('due_date', '<', now())->sum('amount');
        $countOverdue  = (clone $base)->where('status', 'pendente')->whereDate('due_date', '<', now())->count();

        $receivables = $query->orderBy('due_date')->paginate(15);

        // Listas para filtros
        $statuses   = Receivable::STATUS_LABELS;
        $categories = Receivable::CATEGORY_LABELS;

        return view('receivables.index', compact(
            'receivables',
            'totalPending',
            'totalReceived',
            'totalOverdue',
            'countOverdue',
            'statuses',
            'categories'
        ));
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

        $receivable = Receivable::create(array_merge($data, [
            'company_id' => $companyId,
            'status'     => 'pendente',
        ]));

        AuditLogger::action('receivable.created', $receivable);
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

        AuditLogger::action('receivable.updated', $receivable);
        return redirect()->route('receivables.index')->with('success', 'Recebível atualizado com sucesso.');
    }

    public function cancel(Receivable $receivable)
    {
        $this->authorizeReceivable($receivable);

        if (in_array($receivable->status, ['recebida', 'cancelada'])) {
            return redirect()->route('receivables.show', $receivable)
                ->with('error', 'Esta conta não pode ser cancelada.');
        }

        $receivable->update(['status' => 'cancelada']);

        AuditLogger::action('receivable.cancelled', $receivable);
        return redirect()->route('receivables.show', $receivable)
            ->with('success', 'Conta cancelada com sucesso.');
    }

    public function receive(Request $request, Receivable $receivable)
    {
        $this->authorizeReceivable($receivable);
        $company = auth()->user()->company;

        $data = $request->validate([
            'amount_received' => 'required|numeric|min:0.01',
            'received_at'     => 'required|date',
            'payment_method'  => 'nullable|string|max:50',
        ]);

        $receivable->update([
            'amount_received' => $data['amount_received'],
            'received_at'     => $data['received_at'],
            'payment_method'  => $data['payment_method'] ?? null,
            'status'          => 'recebida',
        ]);

        WebhookDispatcher::dispatch($company, 'receivable.received', [
            'id'              => $receivable->id,
            'description'     => $receivable->description,
            'amount'          => (float) $receivable->amount,
            'amount_received' => (float) $receivable->amount_received,
            'received_at'     => $receivable->received_at,
            'customer_id'     => $receivable->customer_id,
        ]);

        AuditLogger::action('receivable.received', $receivable);
        return redirect()->route('receivables.index')->with('success', 'Recebível marcado como recebido.');
    }

    public function bulkReceive(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'ids'            => 'required|array|min:1',
            'ids.*'          => 'integer|exists:receivables,id',
            'received_at'    => 'required|date',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $updated = Receivable::where('company_id', $companyId)
            ->whereIn('id', $data['ids'])
            ->whereIn('status', ['pendente', 'vencida'])
            ->get();

        foreach ($updated as $rec) {
            $rec->update([
                'amount_received' => $rec->amount,
                'received_at'     => $data['received_at'],
                'payment_method'  => $data['payment_method'] ?? null,
                'status'          => 'recebida',
            ]);
        }

        return redirect()->route('receivables.index')
            ->with('success', $updated->count() . ' conta(s) marcada(s) como recebida(s).');
    }

    public function destroy(Receivable $receivable)
    {
        $this->authorizeReceivable($receivable);
        $receivable->delete();
        AuditLogger::action('receivable.deleted', $receivable);
        return redirect()->route('receivables.index')->with('success', 'Recebível removido com sucesso.');
    }

    private function authorizeReceivable(Receivable $receivable): void
    {
        if ($receivable->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
