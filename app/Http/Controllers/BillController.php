<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use App\Models\Bill;
use App\Services\WebhookDispatcher;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Bill::where('company_id', $companyId)
            ->where(fn($q) => $q->whereNull('installment_number')->orWhere('installment_number', '!=', 0));

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('from')) {
            $query->whereDate('due_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('due_date', '<=', $request->to);
        }

        $base = Bill::where('company_id', $companyId)
            ->where(fn($q) => $q->whereNull('installment_number')->orWhere('installment_number', '!=', 0));

        $totalPending  = (clone $base)->where('status', 'pendente')->sum('amount');
        $totalPaid     = (clone $base)->where('status', 'paga')->sum('amount_paid');
        $totalOverdue  = (clone $base)->where('status', 'pendente')->whereDate('due_date', '<', now())->sum('amount');
        $countOverdue  = (clone $base)->where('status', 'pendente')->whereDate('due_date', '<', now())->count();

        $bills = $query->orderBy('due_date')->paginate(15);

        $statuses   = Bill::STATUS_LABELS ?? [
            'pendente'  => 'Pendente',
            'paga'      => 'Paga',
            'vencida'   => 'Vencida',
            'cancelada' => 'Cancelada',
        ];
        $categories = Bill::CATEGORY_LABELS ?? [];

        return view('bills.index', compact(
            'bills',
            'totalPending',
            'totalPaid',
            'totalOverdue',
            'countOverdue',
            'statuses',
            'categories'
        ));
    }

    public function create()
    {
        return view('bills.form');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'description'  => 'required|string|max:255',
            'category'     => 'required|string|in:' . implode(',', array_keys(Bill::CATEGORY_LABELS)),
            'amount'       => 'required|numeric|min:0.01',
            'due_date'     => 'required|date',
            'notes'        => 'nullable|string',
            'billing_type' => 'nullable|in:single,installments,recurrent',
            'installments' => 'nullable|integer|min:2|max:60',
            'recurrence'   => 'nullable|integer|min:2|max:60',
        ]);

        $billingType = $data['billing_type'] ?? 'single';
        $baseDate    = Carbon::parse($data['due_date']);

        // --- Parcelado ---
        if ($billingType === 'installments') {
            $request->validate(['installments' => 'required|integer|min:2|max:60']);
            $n            = (int) $data['installments'];
            $installValue = round($data['amount'] / $n, 2);
            $diff         = round($data['amount'] - ($installValue * $n), 2);

            $parent = Bill::create([
                'company_id'         => $companyId,
                'description'        => $data['description'],
                'category'           => $data['category'],
                'amount'             => $data['amount'],
                'due_date'           => $baseDate,
                'notes'              => $data['notes'] ?? null,
                'status'             => 'pendente',
                'installments'       => $n,
                'installment_number' => 0,
            ]);

            for ($i = 1; $i <= $n; $i++) {
                $parcVal = $installValue + ($i === $n ? $diff : 0);
                Bill::create([
                    'company_id'         => $companyId,
                    'description'        => $data['description'] . ' (' . $i . '/' . $n . ')',
                    'category'           => $data['category'],
                    'amount'             => $parcVal,
                    'due_date'           => $baseDate->copy()->addMonthsNoOverflow($i - 1),
                    'notes'              => $data['notes'] ?? null,
                    'status'             => 'pendente',
                    'installments'       => $n,
                    'installment_number' => $i,
                    'parent_bill_id'     => $parent->id,
                ]);
            }

            AuditLogger::action('bill.created_installments', $parent);
            return redirect()->route('bills.index')
                ->with('success', "Conta parcelada criada: {$n} parcelas de R\$\u00a0" . number_format($installValue, 2, ',', '.') . '.');
        }

        // --- Recorrente ---
        if ($billingType === 'recurrent') {
            $request->validate(['recurrence' => 'required|integer|min:2|max:60']);
            $n = (int) $data['recurrence'];

            $parent = Bill::create([
                'company_id'         => $companyId,
                'description'        => $data['description'],
                'category'           => $data['category'],
                'amount'             => $data['amount'],
                'due_date'           => $baseDate,
                'notes'              => $data['notes'] ?? null,
                'status'             => 'pendente',
                'recurrence'         => $n,
                'installment_number' => 0,
            ]);

            for ($i = 1; $i <= $n; $i++) {
                Bill::create([
                    'company_id'         => $companyId,
                    'description'        => $data['description'] . ' \u2013 ' . $baseDate->copy()->addMonthsNoOverflow($i - 1)->format('m/Y'),
                    'category'           => $data['category'],
                    'amount'             => $data['amount'],
                    'due_date'           => $baseDate->copy()->addMonthsNoOverflow($i - 1),
                    'notes'              => $data['notes'] ?? null,
                    'status'             => 'pendente',
                    'recurrence'         => $n,
                    'installment_number' => $i,
                    'parent_bill_id'     => $parent->id,
                ]);
            }

            AuditLogger::action('bill.created_recurrent', $parent);
            return redirect()->route('bills.index')
                ->with('success', "Despesa recorrente criada: {$n} meses de R\$\u00a0" . number_format($data['amount'], 2, ',', '.') . '.');
        }

        // --- Pagamento único ---
        $bill = Bill::create([
            'company_id'  => $companyId,
            'description' => $data['description'],
            'category'    => $data['category'],
            'amount'      => $data['amount'],
            'due_date'    => $data['due_date'],
            'notes'       => $data['notes'] ?? null,
            'status'      => 'pendente',
        ]);

        AuditLogger::action('bill.created', $bill);
        return redirect()->route('bills.index')->with('success', 'Conta a pagar criada com sucesso.');
    }

    public function show(Bill $bill)
    {
        $this->authorizeBill($bill);
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $this->authorizeBill($bill);
        return view('bills.form', compact('bill'));
    }

    public function update(Request $request, Bill $bill)
    {
        $this->authorizeBill($bill);

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'category'    => 'required|string|in:' . implode(',', array_keys(Bill::CATEGORY_LABELS)),
            'amount'      => 'required|numeric|min:0.01',
            'due_date'    => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        $bill->update($data);

        AuditLogger::action('bill.updated', $bill);
        return redirect()->route('bills.index')->with('success', 'Conta atualizada com sucesso.');
    }

    public function pdf(Bill $bill)
    {
        $this->authorizeBill($bill);

        $bill->load('supplier');
        $company = auth()->user()->company;

        $pdf = Pdf::loadView('bills.pdf', compact('bill', 'company'))
            ->setPaper('a4', 'portrait');

        $filename = 'despesa-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    public function pay(Request $request, Bill $bill)
    {
        $this->authorizeBill($bill);
        $company = auth()->user()->company;

        $data = $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'paid_at'     => 'required|date',
        ]);

        $bill->update([
            'amount_paid' => $data['amount_paid'],
            'paid_at'     => $data['paid_at'],
            'status'      => 'paga',
        ]);

        WebhookDispatcher::dispatch($company, 'bill.paid', [
            'id'          => $bill->id,
            'description' => $bill->description,
            'amount'      => (float) $bill->amount,
            'amount_paid' => (float) $bill->amount_paid,
            'paid_at'     => $bill->paid_at,
        ]);

        AuditLogger::action('bill.paid', $bill);
        return redirect()->route('bills.index')->with('success', 'Conta marcada como paga.');
    }

    public function bulkPay(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $company   = auth()->user()->company;

        $data = $request->validate([
            'ids'            => 'required|array|min:1',
            'ids.*'          => 'integer|exists:bills,id',
            'paid_at'        => 'required|date',
            'payment_method' => 'required|string',
        ]);

        $bills = Bill::where('company_id', $companyId)
            ->whereIn('id', $data['ids'])
            ->whereIn('status', ['pendente', 'vencida'])
            ->get();

        if ($bills->isEmpty()) {
            return redirect()->route('bills.index')
                ->with('error', 'Nenhuma conta elegível encontrada para baixa.');
        }

        DB::transaction(function () use ($bills, $data, $company) {
            foreach ($bills as $bill) {
                $bill->update([
                    'amount_paid'    => $bill->amount,
                    'paid_at'        => $data['paid_at'],
                    'payment_method' => $data['payment_method'],
                    'status'         => 'paga',
                ]);

                WebhookDispatcher::dispatch($company, 'bill.paid', [
                    'id'          => $bill->id,
                    'description' => $bill->description,
                    'amount'      => (float) $bill->amount,
                    'amount_paid' => (float) $bill->amount,
                    'paid_at'     => $bill->paid_at,
                ]);
            }
        });

        $count = $bills->count();

        return redirect()->route('bills.index')
            ->with('success', "{$count} conta(s) marcada(s) como paga(s) com sucesso.");
    }

    public function destroy(Bill $bill)
    {
        $this->authorizeBill($bill);
        $bill->delete();
        AuditLogger::action('bill.deleted', $bill);
        return redirect()->route('bills.index')->with('success', 'Conta removida com sucesso.');
    }

    private function authorizeBill(Bill $bill): void
    {
        if ($bill->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
