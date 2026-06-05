<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private function companyId(): int
    {
        return auth()->user()->company_id;
    }

    public function index(Request $request)
    {
        $customers = Customer::where('company_id', $this->companyId())
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate(20);

        return response()->json($customers);
    }

    public function show(Customer $customer)
    {
        abort_if($customer->company_id !== $this->companyId(), 403);

        return response()->json($customer);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['nullable', 'email', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'document'  => ['nullable', 'string', 'max:20'],
            'address'   => ['nullable', 'string', 'max:500'],
        ]);

        $customer = Customer::create(array_merge($data, [
            'company_id' => $this->companyId(),
        ]));

        return response()->json($customer, 201);
    }

    public function update(Request $request, Customer $customer)
    {
        abort_if($customer->company_id !== $this->companyId(), 403);

        $data = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'document' => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:500'],
        ]);

        $customer->update($data);

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        abort_if($customer->company_id !== $this->companyId(), 403);

        $customer->delete();

        return response()->json(['message' => 'Cliente removido.']);
    }
}
