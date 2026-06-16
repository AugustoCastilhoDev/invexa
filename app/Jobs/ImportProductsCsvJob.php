<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportProductsCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300;

    public function __construct(
        public ProductImport $import
    ) {}

    public function handle(): void
    {
        $this->import->update(['status' => 'processing', 'started_at' => now()]);

        $path      = Storage::path('imports/' . $this->import->filename);
        $companyId = $this->import->company_id;
        $userId    = $this->import->user_id ?? null;

        if (! file_exists($path)) {
            $this->import->update(['status' => 'failed', 'finished_at' => now()]);
            return;
        }

        $handle = fopen($path, 'r');
        // Detecta e remove BOM UTF-8
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        $headers  = array_map('trim', fgetcsv($handle, 0, ';'));
        $errors   = [];
        $imported = 0;
        $updated  = 0;
        $failed   = 0;
        $total    = 0;

        // Cache de categorias e fornecedores da empresa
        $categories = Category::where('company_id', $companyId)->get()->keyBy(fn($c) => Str::lower(trim($c->name)));
        $suppliers  = Supplier::where('company_id', $companyId)->get()->keyBy(fn($s) => Str::lower(trim($s->name)));

        // Descobre se a coluna de custo é 'cost' ou 'cost_price'
        $costColumn = in_array('cost_price', (new Product)->getFillable()) ? 'cost_price' : 'cost';

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
            $price       = $this->parseDecimal($data['preco_venda'] ?? '');
            $cost        = $this->parseDecimal($data['custo'] ?? '');
            $quantity    = (int) ($data['quantidade'] ?? 0);
            $minQty      = isset($data['estoque_minimo']) && $data['estoque_minimo'] !== '' ? (int) $data['estoque_minimo'] : 0;
            $sku         = trim($data['sku'] ?? '');
            $unit        = trim($data['unidade'] ?? 'un');
            $description = trim($data['descricao'] ?? '');
            $categoryKey = Str::lower(trim($data['categoria'] ?? ''));
            $supplierKey = Str::lower(trim($data['fornecedor'] ?? ''));
            $active      = strtolower(trim($data['ativo'] ?? 'sim'));

            $rowErrors = [];
            if ($name === '') $rowErrors[] = 'Nome obrigatório.';
            if ($price === null || $price < 0) $rowErrors[] = 'Preço de venda inválido.';

            if (count($rowErrors)) {
                $errors[] = ['linha' => $line, 'erro' => implode(' | ', $rowErrors)];
                $failed++;
                continue;
            }

            // Resolve category_id
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

            // Resolve supplier_id
            $supplierId = null;
            if ($supplierKey !== '') {
                if ($suppliers->has($supplierKey)) {
                    $supplierId = $suppliers[$supplierKey]->id;
                }
            }

            // Verifica se o SKU já existe — se sim, ATUALIZA somando quantidade
            $existingProduct = $sku !== ''
                ? Product::where('company_id', $companyId)->where('sku', $sku)->first()
                : null;

            if ($existingProduct) {
                // Atualiza dados do produto e SOMA a quantidade
                DB::transaction(function () use (
                    $existingProduct, $name, $price, $cost, $quantity, $minQty,
                    $unit, $description, $categoryId, $supplierId, $active,
                    $costColumn, $companyId, $userId
                ) {
                    $quantityBefore = $existingProduct->quantity;
                    $quantityAfter  = $quantityBefore + $quantity;

                    $existingProduct->update([
                        'name'         => $name,
                        'price'        => $price,
                        $costColumn    => $cost ?? $existingProduct->$costColumn,
                        'quantity'     => $quantityAfter,
                        'min_quantity' => $minQty ?: $existingProduct->min_quantity,
                        'unit'         => $unit ?: $existingProduct->unit,
                        'description'  => $description ?: $existingProduct->description,
                        'category_id'  => $categoryId ?? $existingProduct->category_id,
                        'supplier_id'  => $supplierId ?? $existingProduct->supplier_id,
                        'active'       => in_array($active, ['sim', 's', '1', 'true', 'yes']),
                    ]);

                    // Registra movimentação de entrada apenas se houver quantidade a somar
                    if ($quantity > 0) {
                        StockMovement::create([
                            'company_id'      => $companyId,
                            'product_id'      => $existingProduct->id,
                            'user_id'         => $userId,
                            'type'            => 'entrada',
                            'quantity'        => $quantity,
                            'quantity_before' => $quantityBefore,
                            'quantity_after'  => $quantityAfter,
                            'reason'          => 'ajuste',
                            'notes'           => 'Atualização de estoque via reimportação CSV (SKU duplicado)',
                            'source_type'     => ProductImport::class,
                            'source_id'       => $this->import->id,
                        ]);
                    }
                });

                $updated++;
                $imported++;
                continue;
            }

            // Produto novo — cria normalmente
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
                'active'       => in_array($active, ['sim', 's', '1', 'true', 'yes']),
            ]);

            // Registra movimentação de estoque inicial
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
                    'source_type'     => ProductImport::class,
                    'source_id'       => $this->import->id,
                ]);
            }

            $imported++;
        }

        fclose($handle);
        Storage::delete('imports/' . $this->import->filename);

        $this->import->update([
            'status'        => 'done',
            'total_rows'    => $total,
            'imported_rows' => $imported,
            'failed_rows'   => $failed,
            'errors'        => count($errors) ? $errors : null,
            'finished_at'   => now(),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->import->update([
            'status'      => 'failed',
            'errors'      => [['linha' => 0, 'erro' => $e->getMessage()]],
            'finished_at' => now(),
        ]);
    }

    private function parseDecimal(string $value): ?float
    {
        $value = trim($value);
        if ($value === '') return null;
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return is_numeric($value) ? (float) $value : null;
    }
}
