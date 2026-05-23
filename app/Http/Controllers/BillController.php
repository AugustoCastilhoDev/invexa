<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Bill::where('company_id', $companyId);

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('due_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('due_date', '<=', $request->to);
        }

        $totalPending  = (clone $query)->where('status', 'pendente')->sum('amount');
        $totalPaid     = (clone $query)->where('status', 'paga')->sum('amount_paid');
        $countOverdue  = (clone $query)->where('status', 'pendente')->whereDate('due_date', '<', now())->count();

        $bills = $query->orderBy('due_date')->paginate(15);

        return view('bills.index', compact('bills', 'totalPending', 'totalPaid', 'countOverdue'));
    }

    public function create()
    {
        return view('bills.form');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'amount'      => 'required|numeric|min:0.01',
            'due_date'    => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        Bill::create(array_merge($data, [
            'company_id' => $companyId,
            'status'     => 'pendente',
        ]));

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
            'category'    => 'nullable|string|max:100',
            'amount'      => 'required|numeric|min:0.01',
            'due_date'    => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        $bill->update($data);

        return redirect()->route('bills.index')->with('success', 'Conta atualizada com sucesso.');
    }

    public function markAsPaid(Request $request, Bill $bill)
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

        // Webhook bill.paid
        WebhookDispatcher::dispatch($company, 'bill.paid', [
            'id'          => $bill->id,
            'description' => $bill->description,
            'amount'      => (float) $bill->amount,
            'amount_paid' => (float) $bill->amount_paid,
            'paid_at'     => $bill->paid_at,
        ]);

        return redirect()->route('bills.index')->with('success', 'Conta marcada como paga.');
    }

    public function destroy(Bill $bill)
    {
        $this->authorizeBill($bill);
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Conta removida com sucesso.');
    }

    private function authorizeBill(Bill $bill): void
    {
        if ($bill->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
