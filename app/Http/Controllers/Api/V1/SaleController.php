<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private function companyId(): int
    {
        return auth()->user()->company_id;
    }

    public function index(Request $request)
    {
        $sales = Sale::where('company_id', $this->companyId())
            ->with(['customer:id,name', 'user:id,name'])
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(20);

        return response()->json($sales);
    }

    public function show(Sale $sale)
    {
        abort_if($sale->company_id !== $this->companyId(), 403);

        $sale->load(['items.product:id,name,sku', 'customer:id,name,email', 'user:id,name']);

        return response()->json($sale);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'         => ['nullable', 'exists:customers,id'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'payment_method'      => ['required', 'string'],
            'notes'               => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($data) {
            $companyId = $this->companyId();
            $total     = 0;

            foreach ($data['items'] as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }

            $sale = Sale::create([
                'company_id'     => $companyId,
                'user_id'        => auth()->id(),
                'customer_id'    => $data['customer_id'] ?? null,
                'total'          => $total,
                'payment_method' => $data['payment_method'],
                'notes'          => $data['notes'] ?? null,
                'status'         => 'completed',
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('company_id', $companyId)
                    ->firstOrFail();

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total'      => $item['quantity'] * $item['unit_price'],
                ]);

                // Desconta do estoque
                $product->decrement('quantity', $item['quantity']);
            }

            $sale->load('items.product:id,name');

            return response()->json($sale, 201);
        });
    }
}
