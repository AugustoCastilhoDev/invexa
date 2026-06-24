<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use App\Models\Receivable;
use App\Services\WebhookDispatcher;
use Carbon\Carbon;
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

        $base = Receivable::where('company_id', $companyId);

        $totalPending  = (clone $base)->where('status', 'pendente')->sum('amount');
        $totalReceived = (clone $base)->where('status', 'recebida')->sum('amount_received');
        $totalOverdue  = (clone $base)->where('status', 'pendente')->whereDate('due_date', '<', now())->sum('amount');
        $countOverdue  = (clone $base)->where('status', 'pendente')->whereDate('due_date', '<', now())->count();

        $receivables = $query->orderBy('due_date')->paginate(15);

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
            'customer_id'  => 'nullable|exists:customers,id',
            'description'  => 'required|string|max:255',
            'category'     => 'nullable|string|max:100',
            'amount'       => 'required|numeric|min:0.01',
            'due_date'     => 'required|date',
            'notes'        => 'nullable|string',
            'billing_type' => 'nullable|in:single,installments,recurrent',
            'installments' => 'nullable|integer|min:2|max:60',
            'recurrence'   => 'nullable|integer|min:2|max:60',
        ]);

        $billingType = $data['billing_type'] ?? 'single';
        $baseDate    = Carbon::parse($data['due_date']);

        if ($billingType === 'installments') {
            $request->validate(['installments' => 'required|integer|min:2|max:60']);
            $n            = (int) $data['installments'];
            $installValue = round($data['amount'] / $n, 2);
            $diff         = round($data['amount'] - ($installValue * $n), 2);

            // Registro pai (agrupador)
            $parent = Receivable::create([
                'company_id'   => $companyId,
                'customer_id'  => $data['customer_id'] ?? null,
                'description'  => $data['description'],
                'category'     => $data['category'] ?? null,
                'amount'       => $data['amount'],
                'due_date'     => $baseDate,
                'notes'        => $data['notes'] ?? null,
                'status'       => 'pendente',
                'installments' => $n,
                'installment_number' => 0,
            ]);

            // Gera as parcelas
            for ($i = 1; $i <= $n; $i++) {
                $parcVal = $installValue + ($i === $n ? $diff : 0);
                Receivable::create([
                    'company_id'         => $companyId,
                    'customer_id'        => $data['customer_id'] ?? null,
                    'description'        => $data['description'] . ' (' . $i . '/' . $n . ')',
                    'category'           => $data['category'] ?? null,
                    'amount'             => $parcVal,
                    'due_date'           => $baseDate->copy()->addMonthsNoOverflow($i - 1),
                    'notes'              => $data['notes'] ?? null,
                    'status'             => 'pendente',
                    'installments'       => $n,
                    'installment_number' => $i,
                    'parent_receivable_id' => $parent->id,
                ]);
            }

            AuditLogger::action('receivable.created_installments', $parent);
            return redirect()->route('receivables.index')
                ->with('success', "Conta parcelada criada: {$n} parcelas de R$ " . number_format($installValue, 2, ',', '.') . '.');
        }

        if ($billingType === 'recurrent') {
            $request->validate(['recurrence' => 'required|integer|min:2|max:60']);
            $n = (int) $data['recurrence'];

            // Registro pai (agrupador)
            $parent = Receivable::create([
                'company_id'  => $companyId,
                'customer_id' => $data['customer_id'] ?? null,
                'description' => $data['description'],
                'category'    => $data['category'] ?? null,
                'amount'      => $data['amount'],
                'due_date'    => $baseDate,
                'notes'       => $data['notes'] ?? null,
                'status'      => 'pendente',
                'recurrence'  => $n,
                'installment_number' => 0,
            ]);

            // Gera as recorrências
            for ($i = 1; $i <= $n; $i++) {
                Receivable::create([
                    'company_id'         => $companyId,
                    'customer_id'        => $data['customer_id'] ?? null,
                    'description'        => $data['description'] . ' – ' . $baseDate->copy()->addMonthsNoOverflow($i - 1)->format('m/Y'),
                    'category'           => $data['category'] ?? null,
                    'amount'             => $data['amount'],
                    'due_date'           => $baseDate->copy()->addMonthsNoOverflow($i - 1),
                    'notes'              => $data['notes'] ?? null,
                    'status'             => 'pendente',
                    'recurrence'         => $n,
                    'installment_number' => $i,
                    'parent_receivable_id' => $parent->id,
                ]);
            }

            AuditLogger::action('receivable.created_recurrent', $parent);
            return redirect()->route('receivables.index')
                ->with('success', "Cobrança recorrente criada: {$n} meses de R$ " . number_format($data['amount'], 2, ',', '.') . '.');
        }

        // Cobrança única
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
        $installments = collect();
        if ($receivable->installment_number === 0) {
            $installments = $receivable->installmentReceivables()->orderBy('installment_number')->get();
        }
        return view('receivables.show', compact('receivable', 'installments'));
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
