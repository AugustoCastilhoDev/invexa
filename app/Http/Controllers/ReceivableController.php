<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivableController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Receivable::with('customer')->where('company_id', $companyId);
        if ($request->filled('search'))   { $query->where('description','like','%'.$request->search.'%'); }
        if ($request->filled('status'))   { $query->where('status', $request->status); }
        if ($request->filled('category')) { $query->where('category', $request->category); }
        if ($request->filled('from'))     { $query->whereDate('due_date','>=', $request->from); }
        if ($request->filled('to'))       { $query->whereDate('due_date','<=', $request->to); }
        if ($request->boolean('trashed') && auth()->user()->hasRole(['admin','gerente'])) {
            $query->onlyTrashed();
        }

        $totalAmount    = (clone $query)->sum('amount');
        $totalReceived  = (clone $query)->where('status','recebida')->sum('amount');
        $totalPending   = (clone $query)->where('status','pendente')->sum('amount');
        $totalOverdue   = (clone $query)->where('status','vencida')->sum('amount');
        $countOverdue   = (clone $query)->where('status','vencida')->count();

        $statuses   = Receivable::STATUSES ?? ['pendente'=>'Pendente','recebida'=>'Recebida','vencida'=>'Vencida','cancelada'=>'Cancelada'];
        $categories = Receivable::CATEGORIES ?? [];

        $receivables = $query->orderBy('due_date')->paginate(15)->withQueryString();

        return view('receivables.index', compact(
            'receivables',
            'totalAmount',
            'totalReceived',
            'totalPending',
            'totalOverdue',
            'countOverdue',
            'statuses',
            'categories'
        ));
    }

    public function create()
    {
        $customers = Customer::where('company_id', auth()->user()->company_id)->orderBy('name')->get();
        return view('receivables.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $validated = $request->validate([
            'description'   => ['required','string','max:255'],
            'customer_id'   => ['nullable','exists:customers,id'],
            'amount'        => ['required','numeric','min:0.01'],
            'due_date'      => ['required','date'],
            'status'        => ['required','in:pendente,recebida,cancelada'],
            'payment_method'=> ['nullable','string'],
            'notes'         => ['nullable','string'],
            'installments'  => ['nullable','integer','min:1','max:60'],
            'recurrence'    => ['nullable','in:none,monthly,weekly'],
        ]);

        $installments = (int) ($validated['installments'] ?? 1);
        $recurrence   = $validated['recurrence'] ?? 'none';

        DB::transaction(function () use ($validated, $companyId, $installments, $recurrence) {
            $parentId = null;
            for ($i = 1; $i <= $installments; $i++) {
                $due = Carbon::parse($validated['due_date'])->addMonths($i - 1);
                $r = Receivable::create([
                    'company_id'              => $companyId,
                    'customer_id'             => $validated['customer_id'] ?? null,
                    'description'             => $installments > 1
                        ? $validated['description'] . " ({$i}/{$installments})"
                        : $validated['description'],
                    'amount'                  => round((float)$validated['amount'] / $installments, 2),
                    'due_date'                => $due,
                    'status'                  => $validated['status'],
                    'payment_method'          => $validated['payment_method'] ?? null,
                    'notes'                   => $validated['notes'] ?? null,
                    'installment_number'      => $installments > 1 ? $i : null,
                    'installments_total'      => $installments > 1 ? $installments : null,
                    'recurrence'              => $recurrence !== 'none' ? $recurrence : null,
                    'parent_receivable_id'    => $i > 1 ? $parentId : null,
                ]);
                if ($i === 1) { $parentId = $r->id; }
            }
        });

        return redirect()->route('receivables.index')->with('success', 'Conta a receber criada com sucesso.');
    }

    public function show(Receivable $receivable)
    {
        $receivable->load(['customer','sale']);
        return view('receivables.show', compact('receivable'));
    }

    public function edit(Receivable $receivable)
    {
        $customers = Customer::where('company_id', auth()->user()->company_id)->orderBy('name')->get();
        return view('receivables.edit', compact('receivable','customers'));
    }

    public function update(Request $request, Receivable $receivable)
    {
        $validated = $request->validate([
            'description'    => ['required','string','max:255'],
            'customer_id'    => ['nullable','exists:customers,id'],
            'amount'         => ['required','numeric','min:0.01'],
            'due_date'       => ['required','date'],
            'status'         => ['required','in:pendente,recebida,cancelada'],
            'payment_method' => ['nullable','string'],
            'notes'          => ['nullable','string'],
        ]);
        $receivable->update($validated);
        return redirect()->route('receivables.index')->with('success', 'Conta a receber atualizada.');
    }

    public function destroy(Receivable $receivable)
    {
        $receivable->delete();
        return redirect()->route('receivables.index')->with('success', 'Conta movida para a lixeira.');
    }

    public function receive(Receivable $receivable)
    {
        if ($receivable->status === 'recebida') { return back()->with('error', 'Já recebida.'); }
        $receivable->update(['status' => 'recebida', 'payment_date' => now()]);
        return back()->with('success', 'Conta marcada como recebida.');
    }

    public function bulkReceive(Request $request)
    {
        $ids = $request->input('ids', []);
        $companyId = auth()->user()->company_id;
        Receivable::whereIn('id', $ids)->where('company_id', $companyId)->where('status','pendente')
            ->update(['status' => 'recebida', 'payment_date' => now()]);
        return back()->with('success', count($ids) . ' conta(s) marcada(s) como recebida(s).');
    }

    public function cancel(Receivable $receivable)
    {
        $receivable->update(['status' => 'cancelada']);
        return back()->with('success', 'Conta cancelada.');
    }
}
