<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Bill::with('supplier')->where('company_id', $companyId);
        if ($request->filled('search'))   { $query->where('description','like','%'.$request->search.'%'); }
        if ($request->filled('status'))   { $query->where('status', $request->status); }
        if ($request->filled('category')) { $query->where('category', $request->category); }
        if ($request->filled('from'))     { $query->whereDate('due_date','>=', $request->from); }
        if ($request->filled('to'))       { $query->whereDate('due_date','<=', $request->to); }
        if ($request->boolean('trashed') && auth()->user()->hasRole(['admin','gerente'])) {
            $query->onlyTrashed();
        }

        $totalAmount   = (clone $query)->sum('amount');
        $totalPaid     = (clone $query)->where('status','paga')->sum('amount');
        $totalPending  = (clone $query)->where('status','pendente')->sum('amount');
        $totalOverdue  = (clone $query)->where('status','vencida')->sum('amount');
        $countOverdue  = (clone $query)->where('status','vencida')->count();

        $bills = $query->orderBy('due_date')->paginate(15)->withQueryString();

        return view('bills.index', compact(
            'bills',
            'totalAmount', 'totalPaid', 'totalPending',
            'totalOverdue', 'countOverdue'
        ) + $this->formData());
    }

    public function create()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)->orderBy('name')->get();
        return view('bills.create', compact('suppliers') + $this->formData());
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $validated = $request->validate([
            'description'    => ['required','string','max:255'],
            'supplier_id'    => ['nullable','exists:suppliers,id'],
            'category'       => ['nullable','string'],
            'amount'         => ['required','numeric','min:0.01'],
            'due_date'       => ['required','date'],
            'status'         => ['required','in:pendente,paga,cancelada'],
            'payment_method' => ['nullable','string'],
            'notes'          => ['nullable','string'],
            'installments'   => ['nullable','integer','min:1','max:60'],
            'recurrence'     => ['nullable','in:none,monthly,weekly'],
        ]);

        $installments = (int) ($validated['installments'] ?? 1);
        $recurrence   = $validated['recurrence'] ?? 'none';

        DB::transaction(function () use ($validated, $companyId, $installments, $recurrence) {
            $parentId = null;
            for ($i = 1; $i <= $installments; $i++) {
                $due  = Carbon::parse($validated['due_date'])->addMonths($i - 1);
                $bill = Bill::create([
                    'company_id'         => $companyId,
                    'supplier_id'        => $validated['supplier_id'] ?? null,
                    'category'           => $validated['category'] ?? 'outros',
                    'description'        => $installments > 1
                        ? $validated['description'] . " ({$i}/{$installments})"
                        : $validated['description'],
                    'amount'             => round((float) $validated['amount'] / $installments, 2),
                    'due_date'           => $due,
                    'status'             => $validated['status'],
                    'payment_method'     => $validated['payment_method'] ?? null,
                    'notes'              => $validated['notes'] ?? null,
                    'installments'       => $installments > 1 ? $installments : null,
                    'installment_number' => $installments > 1 ? $i : null,
                    'installments_total' => $installments > 1 ? $installments : null,
                    'recurrence'         => $recurrence !== 'none' ? $recurrence : null,
                    'parent_bill_id'     => $i > 1 ? $parentId : null,
                ]);
                if ($i === 1) { $parentId = $bill->id; }
            }
        });

        return redirect()->route('bills.index')->with('success', 'Conta a pagar criada com sucesso.');
    }

    public function show(Bill $bill)
    {
        $bill->load(['supplier','purchaseOrder']);
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)->orderBy('name')->get();
        return view('bills.edit', compact('bill', 'suppliers') + $this->formData());
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'description'    => ['required','string','max:255'],
            'supplier_id'    => ['nullable','exists:suppliers,id'],
            'category'       => ['nullable','string'],
            'amount'         => ['required','numeric','min:0.01'],
            'due_date'       => ['required','date'],
            'status'         => ['required','in:pendente,paga,cancelada'],
            'payment_method' => ['nullable','string'],
            'notes'          => ['nullable','string'],
        ]);
        $bill->update($validated);
        return redirect()->route('bills.index')->with('success', 'Conta a pagar atualizada.');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Conta movida para a lixeira.');
    }

    public function pay(Bill $bill)
    {
        if ($bill->status === 'paga') {
            return back()->with('error', 'Esta conta já está paga.');
        }
        $bill->update([
            'status'  => 'paga',
            'paid_at' => now(),
        ]);
        return back()->with('success', 'Conta marcada como paga.');
    }

    public function bulkPay(Request $request)
    {
        $ids       = $request->input('ids', []);
        $companyId = auth()->user()->company_id;

        Bill::whereIn('id', $ids)
            ->where('company_id', $companyId)
            ->where('status', 'pendente')
            ->update([
                'status'  => 'paga',
                'paid_at' => now(),
            ]);

        return back()->with('success', count($ids) . ' conta(s) marcada(s) como paga(s).');
    }

    public function cancel(Bill $bill)
    {
        $bill->update(['status' => 'cancelada']);
        return back()->with('success', 'Conta cancelada.');
    }

    // ── Dados comuns para views ─────────────────────────────────────

    private function formData(): array
    {
        return [
            'categories'     => Bill::CATEGORIES,
            'paymentMethods' => Bill::PAYMENT_METHODS,
            'statuses'       => [
                'pendente'  => 'Pendente',
                'paga'      => 'Paga',
                'vencida'   => 'Vencida',
                'cancelada' => 'Cancelada',
            ],
        ];
    }
}
