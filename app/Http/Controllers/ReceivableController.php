<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Marca como vencida qualquer pendente com due_date < hoje
        Receivable::forCompany($companyId)
            ->where('status', 'pendente')
            ->where('due_date', '<', today())
            ->update(['status' => 'vencida']);

        $query = Receivable::with('customer', 'sale')->forCompany($companyId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('from')) {
            $query->whereDate('due_date', '>=', Carbon::parse($request->from)->startOfDay());
        }
        if ($request->filled('to')) {
            $query->whereDate('due_date', '<=', Carbon::parse($request->to)->endOfDay());
        }
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', $search));
            });
        }

        $totalPending   = Receivable::forCompany($companyId)->whereIn('status', ['pendente', 'vencida'])->sum('amount');
        $totalOverdue   = Receivable::forCompany($companyId)->where('status', 'vencida')->sum('amount');
        $totalReceived  = Receivable::forCompany($companyId)->where('status', 'recebida')->sum('amount_received');
        $countOverdue   = Receivable::forCompany($companyId)->where('status', 'vencida')->count();

        $receivables = $query->orderBy('due_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $customers   = Customer::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $categories  = Receivable::CATEGORY_LABELS;
        $statuses    = Receivable::STATUS_LABELS;

        return view('receivables.index', compact(
            'receivables', 'customers', 'categories', 'statuses',
            'totalPending', 'totalOverdue', 'totalReceived', 'countOverdue'
        ));
    }

    public function create()
    {
        $companyId      = auth()->user()->company_id;
        $customers      = Customer::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $categories     = Receivable::CATEGORY_LABELS;
        $paymentMethods = Receivable::PAYMENT_METHODS;
        return view('receivables.create', compact('customers', 'categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'due_date'    => ['required', 'date'],
            'category'    => ['required', 'in:' . implode(',', array_keys(Receivable::CATEGORY_LABELS))],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'notes'       => ['nullable', 'string'],
        ]);

        $status = Carbon::parse($validated['due_date'])->isPast() ? 'vencida' : 'pendente';

        Receivable::create(array_merge($validated, [
            'company_id'      => $companyId,
            'status'          => $status,
            'amount_received' => 0,
        ]));

        return redirect()->route('receivables.index')
            ->with('success', 'Conta a receber registrada com sucesso.');
    }

    public function show(Receivable $receivable)
    {
        abort_if($receivable->company_id !== auth()->user()->company_id, 403);
        $receivable->load('customer', 'sale');
        return view('receivables.show', compact('receivable'));
    }

    public function edit(Receivable $receivable)
    {
        abort_if($receivable->company_id !== auth()->user()->company_id, 403);
        $companyId      = auth()->user()->company_id;
        $customers      = Customer::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $categories     = Receivable::CATEGORY_LABELS;
        $paymentMethods = Receivable::PAYMENT_METHODS;
        return view('receivables.edit', compact('receivable', 'customers', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, Receivable $receivable)
    {
        abort_if($receivable->company_id !== auth()->user()->company_id, 403);

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'due_date'    => ['required', 'date'],
            'category'    => ['required', 'in:' . implode(',', array_keys(Receivable::CATEGORY_LABELS))],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'notes'       => ['nullable', 'string'],
        ]);

        $receivable->update($validated);

        return redirect()->route('receivables.show', $receivable)
            ->with('success', 'Conta a receber atualizada com sucesso.');
    }

    /**
     * Registra recebimento total ou parcial.
     */
    public function receive(Request $request, Receivable $receivable)
    {
        abort_if($receivable->company_id !== auth()->user()->company_id, 403);
        abort_if(in_array($receivable->status, ['recebida', 'cancelada']), 422);

        $validated = $request->validate([
            'amount_received' => ['required', 'numeric', 'min:0.01'],
            'received_at'     => ['required', 'date'],
            'payment_method'  => ['required', 'in:' . implode(',', array_keys(Receivable::PAYMENT_METHODS))],
        ], [
            'amount_received.required' => 'Informe o valor recebido.',
            'received_at.required'     => 'Informe a data do recebimento.',
            'payment_method.required'  => 'Selecione a forma de pagamento.',
        ]);

        $newAmountReceived = (float) $receivable->amount_received + (float) $validated['amount_received'];
        $status = $newAmountReceived >= (float) $receivable->amount ? 'recebida' : 'pendente';

        $receivable->update([
            'amount_received' => $newAmountReceived,
            'received_at'     => $validated['received_at'],
            'payment_method'  => $validated['payment_method'],
            'status'          => $status,
        ]);

        return redirect()->route('receivables.show', $receivable)
            ->with('success', $status === 'recebida' ? 'Conta marcada como recebida!' : 'Recebimento parcial registrado.');
    }

    public function cancel(Receivable $receivable)
    {
        abort_if($receivable->company_id !== auth()->user()->company_id, 403);
        abort_if($receivable->status === 'cancelada', 422);
        $receivable->update(['status' => 'cancelada']);
        return back()->with('success', 'Conta a receber cancelada.');
    }

    public function destroy(Receivable $receivable)
    {
        abort_if($receivable->company_id !== auth()->user()->company_id, 403);
        $receivable->delete();
        return redirect()->route('receivables.index')
            ->with('success', 'Conta a receber excluída com sucesso.');
    }
}
