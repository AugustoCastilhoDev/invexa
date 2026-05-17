<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Atualiza para 'vencida' qualquer pendente com due_date < hoje
        Bill::forCompany($companyId)
            ->where('status', 'pendente')
            ->where('due_date', '<', today())
            ->update(['status' => 'vencida']);

        $query = Bill::with('supplier')->forCompany($companyId);

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
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', $search));
            });
        }

        $totalPending  = Bill::forCompany($companyId)->whereIn('status', ['pendente', 'vencida'])->sum('amount');
        $totalOverdue  = Bill::forCompany($companyId)->where('status', 'vencida')->sum('amount');
        $totalPaid     = Bill::forCompany($companyId)->where('status', 'paga')->sum('amount_paid');
        $countOverdue  = Bill::forCompany($companyId)->where('status', 'vencida')->count();

        $bills = $query->orderBy('due_date')->orderByDesc('id')->paginate(15)->withQueryString();

        $suppliers  = Supplier::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $categories = Bill::CATEGORY_LABELS;
        $statuses   = Bill::STATUS_LABELS;

        return view('bills.index', compact(
            'bills', 'suppliers', 'categories', 'statuses',
            'totalPending', 'totalOverdue', 'totalPaid', 'countOverdue'
        ));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $categories     = Bill::CATEGORY_LABELS;
        $paymentMethods = Bill::PAYMENT_METHODS;
        return view('bills.create', compact('suppliers', 'categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'description'    => ['required', 'string', 'max:255'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'due_date'       => ['required', 'date'],
            'category'       => ['required', 'in:' . implode(',', array_keys(Bill::CATEGORY_LABELS))],
            'supplier_id'    => ['nullable', 'exists:suppliers,id'],
            'notes'          => ['nullable', 'string'],
        ]);

        $status = Carbon::parse($validated['due_date'])->isPast() ? 'vencida' : 'pendente';

        Bill::create(array_merge($validated, [
            'company_id'  => $companyId,
            'status'      => $status,
            'amount_paid' => 0,
        ]));

        return redirect()->route('bills.index')
            ->with('success', 'Conta a pagar registrada com sucesso.');
    }

    public function show(Bill $bill)
    {
        abort_if($bill->company_id !== auth()->user()->company_id, 403);
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        abort_if($bill->company_id !== auth()->user()->company_id, 403);
        $companyId      = auth()->user()->company_id;
        $suppliers      = Supplier::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $categories     = Bill::CATEGORY_LABELS;
        $paymentMethods = Bill::PAYMENT_METHODS;
        return view('bills.edit', compact('bill', 'suppliers', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, Bill $bill)
    {
        abort_if($bill->company_id !== auth()->user()->company_id, 403);

        $validated = $request->validate([
            'description'    => ['required', 'string', 'max:255'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'due_date'       => ['required', 'date'],
            'category'       => ['required', 'in:' . implode(',', array_keys(Bill::CATEGORY_LABELS))],
            'supplier_id'    => ['nullable', 'exists:suppliers,id'],
            'notes'          => ['nullable', 'string'],
        ]);

        $bill->update($validated);

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Conta atualizada com sucesso.');
    }

    /**
     * Registra pagamento (total ou parcial).
     */
    public function pay(Request $request, Bill $bill)
    {
        abort_if($bill->company_id !== auth()->user()->company_id, 403);
        abort_if(in_array($bill->status, ['paga', 'cancelada']), 422);

        $validated = $request->validate([
            'amount_paid'    => ['required', 'numeric', 'min:0.01'],
            'paid_at'        => ['required', 'date'],
            'payment_method' => ['required', 'in:' . implode(',', array_keys(Bill::PAYMENT_METHODS))],
        ], [
            'amount_paid.required'    => 'Informe o valor pago.',
            'paid_at.required'        => 'Informe a data do pagamento.',
            'payment_method.required' => 'Selecione a forma de pagamento.',
        ]);

        $newAmountPaid = (float) $bill->amount_paid + (float) $validated['amount_paid'];
        $status = $newAmountPaid >= (float) $bill->amount ? 'paga' : 'pendente';

        $bill->update([
            'amount_paid'    => $newAmountPaid,
            'paid_at'        => $validated['paid_at'],
            'payment_method' => $validated['payment_method'],
            'status'         => $status,
        ]);

        return redirect()->route('bills.show', $bill)
            ->with('success', $status === 'paga' ? 'Conta marcada como paga!' : 'Pagamento parcial registrado.');
    }

    public function cancel(Bill $bill)
    {
        abort_if($bill->company_id !== auth()->user()->company_id, 403);
        abort_if($bill->status === 'cancelada', 422);
        $bill->update(['status' => 'cancelada']);
        return back()->with('success', 'Conta cancelada.');
    }

    public function destroy(Bill $bill)
    {
        abort_if($bill->company_id !== auth()->user()->company_id, 403);
        $bill->delete();
        return redirect()->route('bills.index')
            ->with('success', 'Conta excluída com sucesso.');
    }
}
