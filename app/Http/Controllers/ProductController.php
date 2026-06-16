<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Product::where('company_id', $companyId)
            ->with('category')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'ativo');
        }
        if ($request->boolean('low_stock')) {
            $query->where('active', true)
                  ->where('min_quantity', '>', 0)
                  ->whereColumn('quantity', '<=', 'min_quantity');
        }

        $products        = $query->paginate(15)->withQueryString();
        $categories      = Category::where('company_id', $companyId)->orderBy('name')->get();
        $totalProducts   = Product::where('company_id', $companyId)->count();
        $categoriesCount = $categories->count();
        $lowStockCount   = Product::where('company_id', $companyId)
                               ->where('active', true)
                               ->where('min_quantity', '>', 0)
                               ->whereColumn('quantity', '<=', 'min_quantity')
                               ->count();
        $lowStockAlert   = $lowStockCount;

        return view('products.index', compact(
            'products',
            'categories',
            'totalProducts',
            'categoriesCount',
            'lowStockCount',
            'lowStockAlert'
        ));
    }

    public function create()
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('products')) {
            return redirect()->route('products.index')
                ->with('error', $this->limitMessage('produtos', $company->limit('products')));
        }

        $companyId  = Auth::user()->company_id;
        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $suppliers  = Supplier::where('company_id', $companyId)->orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $company   = Auth::user()->company;
        $companyId = Auth::user()->company_id;

        if ($company && !$company->canAdd('products')) {
            return redirect()->route('products.index')
                ->with('error', $this->limitMessage('produtos', $company->limit('products')));
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'sku'          => [
                'nullable', 'string', 'max:100',
                Rule::unique('products', 'sku')->where('company_id', $companyId),
            ],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'supplier_id'  => ['nullable', 'exists:suppliers,id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'cost_price'   => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['required', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'description'  => ['nullable', 'string'],
            'unit'         => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'  => 'O nome do produto é obrigatório.',
            'sku.unique'     => 'Já existe um produto com este SKU.',
            'price.required' => 'O preço de venda é obrigatório.',
        ]);

        $product = null;

        DB::transaction(function () use ($validated, $companyId, $request, &$product) {
            $product = Product::create(array_merge($validated, [
                'company_id' => $companyId,
                'active'     => $request->boolean('active', true),
            ]));

            if ($product->quantity > 0) {
                StockMovement::create([
                    'product_id'      => $product->id,
                    'company_id'      => $companyId,
                    'user_id'         => Auth::id(),
                    'type'            => 'entrada',
                    'quantity'        => $product->quantity,
                    'quantity_before' => 0,
                    'quantity_after'  => $product->quantity,
                    'reason'          => 'cadastro',
                    'notes'           => 'Estoque inicial no cadastro do produto.',
                ]);
            }
        });

        AuditLogger::action('product.created', $product);
        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso.');
    }

    public function show(Product $product)
    {
        $this->authorizeProduct($product);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        $companyId  = Auth::user()->company_id;
        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $suppliers  = Supplier::where('company_id', $companyId)->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        $companyId = Auth::user()->company_id;

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'sku'          => [
                'nullable', 'string', 'max:100',
                Rule::unique('products', 'sku')
                    ->where('company_id', $companyId)
                    ->ignore($product->id),
            ],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'supplier_id'  => ['nullable', 'exists:suppliers,id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'cost_price'   => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['required', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'description'  => ['nullable', 'string'],
            'unit'         => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'  => 'O nome do produto é obrigatório.',
            'sku.unique'     => 'Já existe um produto com este SKU.',
            'price.required' => 'O preço de venda é obrigatório.',
        ]);

        DB::transaction(function () use ($product, $validated, $request, $companyId) {
            $quantityBefore = $product->quantity;
            $quantityNew    = (int) $validated['quantity'];

            $product->update(array_merge($validated, [
                'active' => $request->boolean('active'),
            ]));

            if ($quantityNew !== $quantityBefore) {
                $diff = $quantityNew - $quantityBefore;
                StockMovement::create([
                    'product_id'      => $product->id,
                    'company_id'      => $companyId,
                    'user_id'         => Auth::id(),
                    'type'            => $diff > 0 ? 'entrada' : 'saida',
                    'quantity'        => abs($diff),
                    'quantity_before' => $quantityBefore,
                    'quantity_after'  => $quantityNew,
                    'reason'          => 'ajuste',
                    'notes'           => 'Ajuste manual de estoque via edição do produto.',
                ]);
            }
        });

        AuditLogger::action('product.updated', $product);
        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);
        $product->delete();
        AuditLogger::action('product.deleted', $product);
        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso.');
    }

    // ── Importação CSV ───────────────────────────────────────────────

    public function importTemplate()
    {
        $headers = ['nome','sku','categoria','fornecedor','preco_venda','custo','quantidade','estoque_minimo','unidade','descricao','ativo'];
        $example = ['Produto Exemplo','SKU001','Categoria A','Fornecedor X','29,90','15,00','100','10','un','Descrição do produto','sim'];

        $csv  = implode(';', $headers) . "\n";
        $csv .= implode(';', $example) . "\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-produtos.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'csv_file.required' => 'Selecione um arquivo CSV.',
            'csv_file.mimes'    => 'O arquivo deve ser do tipo CSV.',
            'csv_file.max'      => 'O arquivo não pode ultrapassar 5 MB.',
        ]);

        $user      = Auth::user();
        $companyId = $user->company_id;
        $userId    = $user->id;
        $filename  = Str::uuid() . '.csv';

        $request->file('csv_file')->storeAs('imports', $filename);

        $import = ProductImport::create([
            'company_id' => $companyId,
            'user_id'    => $userId,
            'filename'   => $filename,
            'status'     => 'processing',
            'started_at' => now(),
        ]);

        // ── Processamento síncrono (sem queue/worker) ──
        $path = Storage::path('imports/' . $filename);

        if (! file_exists($path)) {
            $import->update(['status' => 'failed', 'finished_at' => now()]);
            return redirect()->route('products.import')->with('error', 'Arquivo não encontrado após upload.');
        }

        $handle = fopen($path, 'r');
        $bom    = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        $headers  = array_map('trim', fgetcsv($handle, 0, ';'));
        $errors   = [];
        $imported = 0;
        $failed   = 0;
        $total    = 0;

        $costColumn = in_array('cost_price', (new Product)->getFillable()) ? 'cost_price' : 'cost';

        // Cache de categorias e fornecedores
        $categories = Category::where('company_id', $companyId)->get()->keyBy(fn($c) => Str::lower(trim($c->name)));
        $suppliers  = Supplier::where('company_id', $companyId)->get()->keyBy(fn($s) => Str::lower(trim($s->name)));

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $total++;
            $line = $total + 1;

            if (count($row) < 3) {
                $errors[] = ['linha' => $line, 'erro' => 'Linha com colunas insuficientes.'];
                $failed++;
                continue;
            }

            $data = array_combine($headers, array_pad($row, count($headers), null));

            $name        = trim($data['nome'] ?? '');
            $price       = $this->parseCsvDecimal($data['preco_venda'] ?? '');
            $cost        = $this->parseCsvDecimal($data['custo'] ?? '');
            $quantity    = (int) ($data['quantidade'] ?? 0);
            $minQty      = isset($data['estoque_minimo']) && $data['estoque_minimo'] !== '' ? (int) $data['estoque_minimo'] : 0;
            $sku         = trim($data['sku'] ?? '');
            $unit        = trim($data['unidade'] ?? 'un');
            $description = trim($data['descricao'] ?? '');
            $categoryKey = Str::lower(trim($data['categoria'] ?? ''));
            $supplierKey = Str::lower(trim($data['fornecedor'] ?? ''));
            $active      = strtolower(trim($data['ativo'] ?? 'sim'));
            $isActive    = in_array($active, ['sim', 's', '1', 'true', 'yes']);

            // Validações básicas
            $rowErrors = [];
            if ($name === '') $rowErrors[] = 'Nome obrigatório.';
            if ($price === null || $price < 0) $rowErrors[] = 'Preço de venda inválido.';

            if (count($rowErrors)) {
                $errors[] = ['linha' => $line, 'erro' => implode(' | ', $rowErrors)];
                $failed++;
                continue;
            }

            // Resolve categoria — cria se não existir
            $categoryId = null;
            if ($categoryKey !== '') {
                if ($categories->has($categoryKey)) {
                    $categoryId = $categories[$categoryKey]->id;
                } else {
                    $cat = Category::create(['company_id' => $companyId, 'name' => trim($data['categoria'])]);
                    $categories->put($categoryKey, $cat);
                    $categoryId = $cat->id;
                }
            }

            // Resolve fornecedor — cria se não existir
            $supplierId = null;
            if ($supplierKey !== '') {
                if ($suppliers->has($supplierKey)) {
                    $supplierId = $suppliers[$supplierKey]->id;
                } else {
                    $sup = Supplier::create(['company_id' => $companyId, 'name' => trim($data['fornecedor'])]);
                    $suppliers->put($supplierKey, $sup);
                    $supplierId = $sup->id;
                }
            }

            // SKU duplicado → ATUALIZA (sem somar quantidade na reimportação)
            if ($sku !== '') {
                $existing = Product::where('company_id', $companyId)->where('sku', $sku)->first();

                if ($existing) {
                    DB::transaction(function () use (
                        $existing, $name, $price, $cost, $quantity, $minQty,
                        $unit, $description, $categoryId, $supplierId, $isActive,
                        $costColumn, $companyId, $userId
                    ) {
                        $qBefore = $existing->quantity;

                        $existing->update([
                            'name'         => $name,
                            'price'        => $price,
                            $costColumn    => $cost ?? $existing->$costColumn,
                            'min_quantity' => $minQty ?: $existing->min_quantity,
                            'unit'         => $unit ?: $existing->unit,
                            'description'  => $description ?: $existing->description,
                            'category_id'  => $categoryId ?? $existing->category_id,
                            'supplier_id'  => $supplierId ?? $existing->supplier_id,
                            'active'       => $isActive,
                        ]);

                        // Registra movimentação somente se a quantidade mudar
                        if ($quantity > 0 && $quantity !== $qBefore) {
                            $existing->update(['quantity' => $quantity]);
                            StockMovement::create([
                                'company_id'      => $companyId,
                                'product_id'      => $existing->id,
                                'user_id'         => $userId,
                                'type'            => $quantity > $qBefore ? 'entrada' : 'saida',
                                'quantity'        => abs($quantity - $qBefore),
                                'quantity_before' => $qBefore,
                                'quantity_after'  => $quantity,
                                'reason'          => 'ajuste',
                                'notes'           => 'Ajuste via reimportação CSV (SKU existente)',
                            ]);
                        }
                    });

                    $imported++;
                    continue;
                }
            }

            // Produto novo
            DB::transaction(function () use (
                $name, $price, $cost, $quantity, $minQty, $sku, $unit,
                $description, $categoryId, $supplierId, $isActive,
                $costColumn, $companyId, $userId
            ) {
                $product = Product::create([
                    'company_id'   => $companyId,
                    'name'         => $name,
                    'sku'          => $sku ?: null,
                    'price'        => $price,
                    $costColumn    => $cost ?? 0,
                    'quantity'     => $quantity,
                    'min_quantity' => $minQty,
                    'unit'         => $unit ?: 'un',
                    'description'  => $description ?: null,
                    'category_id'  => $categoryId,
                    'supplier_id'  => $supplierId,
                    'active'       => $isActive,
                ]);

                if ($quantity > 0) {
                    StockMovement::create([
                        'company_id'      => $companyId,
                        'product_id'      => $product->id,
                        'user_id'         => $userId,
                        'type'            => 'entrada',
                        'quantity'        => $quantity,
                        'quantity_before' => 0,
                        'quantity_after'  => $quantity,
                        'reason'          => 'ajuste',
                        'notes'           => 'Estoque inicial via importação CSV',
                    ]);
                }
            });

            $imported++;
        }

        fclose($handle);
        Storage::delete('imports/' . $filename);

        $import->update([
            'status'        => 'done',
            'total_rows'    => $total,
            'imported_rows' => $imported,
            'failed_rows'   => $failed,
            'errors'        => count($errors) ? $errors : null,
            'finished_at'   => now(),
        ]);

        $msg = "Importação concluída! {$imported} produto(s) processado(s)";
        if ($failed > 0) $msg .= ", {$failed} com erro";
        $msg .= '.';

        return redirect()->route('products.import')->with('success', $msg);
    }

    public function importIndex()
    {
        $imports = ProductImport::where('company_id', Auth::user()->company_id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('products.import', compact('imports'));
    }

    // ── Guards privados ────────────────────────────────────────────

    private function authorizeProduct(Product $product): void
    {
        if ($product->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite): string
    {
        $plano = strtoupper(Auth::user()->company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}). ✨ Faça upgrade para continuar.";
    }

    private function parseCsvDecimal(string $value): ?float
    {
        $value = trim($value);
        if ($value === '') return null;
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return is_numeric($value) ? (float) $value : null;
    }
}
