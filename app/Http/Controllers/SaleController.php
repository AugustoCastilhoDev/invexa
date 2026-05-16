<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Sale::with(['items.product', 'customer'])->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', $search)
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', $search));
            });
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
            'customer_id'            => ['nullable', 'exists:customers,id'],
            'customer_name'          => ['nullable', 'string', 'max:255'],
            'sale_date'              => ['required', 'date'],
            'status'                 => ['required', 'in:concluida,pendente,cancelada'],
            'notes'                  => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'items.*.price'          => ['required', 'numeric', 'min:0'],
        ]);

        // Se um cliente cadastrado foi selecionado, usa o nome dele
        $customerName = $validated['customer_name'] ?? null;
        if (!empty($validated['customer_id'])) {
            $customer     = Customer::find($validated['customer_id']);
            $customerName = $customer?->name ?? $customerName;
        }

        try {
            DB::transaction(function () use ($validated, $companyId, $customerName) {
                $total = collect($validated['items'])
                    ->sum(fn($i) => $i['quantity'] * $i['price']);

                $sale = Sale::create([
                    'company_id'    => $companyId,
                    'customer_id'   => $validated['customer_id'] ?? null,
                    'customer_name' => $customerName,
                    'sale_date'     => Carbon::parse($validated['sale_date'], config('app.timezone')),
                    'status'        => $validated['status'],
                    'notes'         => $validated['notes'] ?? null,
                    'total'         => $total,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Estoque insuficiente para \"{$product->name}\". Disponível: {$product->quantity} un.");
                    }

                    $before = $product->quantity;
                    $after  = $before - $item['quantity'];

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                        'subtotal'   => $item['quantity'] * $item['price'],
                    ]);

                    $product->update(['quantity' => $after]);

                    StockMovement::create([
                        'product_id'      => $product->id,
                        'company_id'      => $companyId,
                        'user_id'         => auth()->id(),
                        'type'            => 'saida',
                        'quantity'        => -$item['quantity'],
                        'quantity_before' => $before,
                        'quantity_after'  => $after,
                        'reason'          => 'venda',
                        'notes'           => "Venda #{$sale->id}" . ($customerName ? " — {$customerName}" : ''),
                        'source_type'     => Sale::class,
                        'source_id'       => $sale->id,
                    ]);
                }
            });

            return redirect()->route('sales.index')
                ->with('success', 'Venda registrada com sucesso.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'customer']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $companyId = auth()->user()->company_id;
        $sale->load(['items.product', 'customer']);
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
            'customer_id'            => ['nullable', 'exists:customers,id'],
            'customer_name'          => ['nullable', 'string', 'max:255'],
            'sale_date'              => ['required', 'date'],
            'status'                 => ['required', 'in:concluida,pendente,cancelada'],
            'notes'                  => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'items.*.price'          => ['required', 'numeric', 'min:0'],
        ]);

        $customerName = $validated['customer_name'] ?? null;
        if (!empty($validated['customer_id'])) {
            $customer     = Customer::find($validated['customer_id']);
            $customerName = $customer?->name ?? $customerName;
        }

        try {
            DB::transaction(function () use ($sale, $validated, $companyId, $customerName) {
                $sale->load('items.product');

                foreach ($sale->items as $oldItem) {
                    $oldProduct = Product::find($oldItem->product_id);
                    if ($oldProduct) {
                        $before = $oldProduct->quantity;
                        $after  = $before + $oldItem->quantity;
                        $oldProduct->update(['quantity' => $after]);

                        StockMovement::create([
                            'product_id'      => $oldProduct->id,
                            'company_id'      => $companyId,
                            'user_id'         => auth()->id(),
                            'type'            => 'entrada',
                            'quantity'        => +$oldItem->quantity,
                            'quantity_before' => $before,
                            'quantity_after'  => $after,
                            'reason'          => 'devolucao',
                            'notes'           => "Estorno da edição da Venda #{$sale->id}",
                            'source_type'     => Sale::class,
                            'source_id'       => $sale->id,
                        ]);
                    }
                }

                $sale->items()->delete();

                $total = collect($validated['items'])
                    ->sum(fn($i) => $i['quantity'] * $i['price']);

                $sale->update([
                    'customer_id'   => $validated['customer_id'] ?? null,
                    'customer_name' => $customerName,
                    'sale_date'     => Carbon::parse($validated['sale_date'], config('app.timezone')),
                    'status'        => $validated['status'],
                    'notes'         => $validated['notes'] ?? null,
                    'total'         => $total,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Estoque insuficiente para \"{$product->name}\". Disponível: {$product->quantity} un.");
                    }

                    $before = $product->quantity;
                    $after  = $before - $item['quantity'];

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                        'subtotal'   => $item['quantity'] * $item['price'],
                    ]);

                    $product->update(['quantity' => $after]);

                    StockMovement::create([
                        'product_id'      => $product->id,
                        'company_id'      => $companyId,
                        'user_id'         => auth()->id(),
                        'type'            => 'saida',
                        'quantity'        => -$item['quantity'],
                        'quantity_before' => $before,
                        'quantity_after'  => $after,
                        'reason'          => 'venda',
                        'notes'           => "Venda #{$sale->id} (editada)" . ($customerName ? " — {$customerName}" : ''),
                        'source_type'     => Sale::class,
                        'source_id'       => $sale->id,
                    ]);
                }
            });

            return redirect()->route('sales.index')
                ->with('success', 'Venda atualizada com sucesso.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Sale $sale)
    {
        $companyId = auth()->user()->company_id;

        DB::transaction(function () use ($sale, $companyId) {
            $sale->load('items.product');

            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $before = $product->quantity;
                    $after  = $before + $item->quantity;
                    $product->update(['quantity' => $after]);

                    StockMovement::create([
                        'product_id'      => $product->id,
                        'company_id'      => $companyId,
                        'user_id'         => auth()->id(),
                        'type'            => 'entrada',
                        'quantity'        => +$item->quantity,
                        'quantity_before' => $before,
                        'quantity_after'  => $after,
                        'reason'          => 'devolucao',
                        'notes'           => "Estorno por exclusão da Venda #{$sale->id}",
                        'source_type'     => Sale::class,
                        'source_id'       => $sale->id,
                    ]);
                }
            }

            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Venda excluída e estoque restaurado com sucesso.');
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['items.product', 'customer']);
        return view('sales.invoice', compact('sale'));
    }
}
