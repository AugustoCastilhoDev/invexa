<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Sale::with(['items.product'])->where('company_id', $companyId);

        if ($request->filled('search')) {
            $query->where('customer_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('sale_date', '>=', Carbon::parse($request->from)->startOfDay());
        }

        if ($request->filled('to')) {
            $query->whereDate('sale_date', '<=', Carbon::parse($request->to)->endOfDay());
        }

        $salesCount     = (clone $query)->count();
        $salesRevenue   = (clone $query)->sum('total');
        $completedSales = (clone $query)->where('status', 'concluida')->count();
        $pendingSales   = (clone $query)->where('status', 'pendente')->count();

        $sales = $query->orderByDesc('sale_date')->orderByDesc('id')->paginate(10);

        return view('sales.index', compact('sales', 'salesCount', 'salesRevenue', 'completedSales', 'pendingSales'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $products  = Product::where('company_id', $companyId)
                            ->where('active', true)
                            ->orderBy('name')
                            ->get(['id', 'name', 'quantity', 'price']);

        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'customer_name'          => ['nullable', 'string', 'max:255'],
            'sale_date'              => ['required', 'date'],
            'status'                 => ['required', 'in:concluida,pendente,cancelada'],
            'notes'                  => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'items.*.price'          => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated, $companyId) {
                $total = 0;

                foreach ($validated['items'] as $item) {
                    $total += $item['quantity'] * $item['price'];
                }

                $sale = Sale::create([
                    'company_id'    => $companyId,
                    'customer_name' => $validated['customer_name'] ?? null,
                    'sale_date'     => Carbon::parse($validated['sale_date'], config('app.timezone')),
                    'status'        => $validated['status'],
                    'notes'         => $validated['notes'] ?? null,
                    'total'         => $total,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Estoque insuficiente para o produto \"{$product->name}\". Disponível: {$product->quantity} un.");
                    }

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                        'subtotal'   => $item['quantity'] * $item['price'],
                    ]);

                    $product->decrement('quantity', $item['quantity']);
                }
            });

            return redirect()->route('sales.index')
                ->with('success', 'Venda registrada com sucesso.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product']);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $companyId = auth()->user()->company_id;
        $sale->load(['items.product']);
        $products = Product::where('company_id', $companyId)
                           ->where('active', true)
                           ->orderBy('name')
                           ->get(['id', 'name', 'quantity', 'price']);

        return view('sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'customer_name'          => ['nullable', 'string', 'max:255'],
            'sale_date'              => ['required', 'date'],
            'status'                 => ['required', 'in:concluida,pendente,cancelada'],
            'notes'                  => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'items.*.price'          => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($sale, $validated, $companyId) {
                $sale->load('items.product');

                foreach ($sale->items as $oldItem) {
                    $oldProduct = Product::find($oldItem->product_id);
                    if ($oldProduct) {
                        $oldProduct->increment('quantity', $oldItem->quantity);
                    }
                }

                $sale->items()->delete();

                $total = 0;
                foreach ($validated['items'] as $item) {
                    $total += $item['quantity'] * $item['price'];
                }

                $sale->update([
                    'customer_name' => $validated['customer_name'] ?? null,
                    'sale_date'     => Carbon::parse($validated['sale_date'], config('app.timezone')),
                    'status'        => $validated['status'],
                    'notes'         => $validated['notes'] ?? null,
                    'total'         => $total,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Estoque insuficiente para o produto \"{$product->name}\". Disponível: {$product->quantity} un.");
                    }

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                        'subtotal'   => $item['quantity'] * $item['price'],
                    ]);

                    $product->decrement('quantity', $item['quantity']);
                }
            });

            return redirect()->route('sales.index')
                ->with('success', 'Venda atualizada com sucesso.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale->load('items.product');

            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('quantity', $item->quantity);
                }
            }

            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Venda excuída e estoque restaurado com sucesso.');
    }
}
