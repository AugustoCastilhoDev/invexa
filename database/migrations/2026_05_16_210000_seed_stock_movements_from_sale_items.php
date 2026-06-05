<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Popula stock_movements com o histórico completo de sale_items já existentes.
     * Agrupa por (sale_id, product_id) para evitar duplicatas caso a migration
     * seja executada mais de uma vez em ambientes de desenvolvimento.
     */
    public function up(): void
    {
        // Evita duplicar caso já existam movimentos do tipo 'venda' na tabela
        $alreadySeeded = DB::table('stock_movements')
            ->where('reason', 'venda')
            ->whereNotNull('source_id')
            ->exists();

        if ($alreadySeeded) {
            return;
        }

        // Busca todos os sale_items com dados da venda e do produto
        $items = DB::table('sale_items')
            ->join('sales',    'sale_items.sale_id',    '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'sale_items.id        as item_id',
                'sale_items.sale_id',
                'sale_items.product_id',
                'sale_items.quantity  as qty',
                'sales.company_id',
                'sales.customer_name',
                'sales.created_at     as sale_created_at',
                'products.quantity    as current_qty',
                'products.name        as product_name'
            )
            ->orderBy('sales.created_at')
            ->orderBy('sale_items.id')
            ->get();

        // Para reconstruir quantity_before/after, calculamos o estoque
        // "de trás para frente" a partir do valor atual de cada produto.
        // Agrupamos os itens por produto e processamos em ordem cronológica.
        $productItems = [];
        foreach ($items as $item) {
            $productItems[$item->product_id][] = $item;
        }

        $now = now()->toDateTimeString();
        $rows = [];

        foreach ($productItems as $productId => $productSaleItems) {
            // Estoque atual do produto (ponto de partida para regressão)
            $runningQty = DB::table('products')->where('id', $productId)->value('quantity') ?? 0;

            // Percorre do mais recente para o mais antigo para calcular snapshots
            $reversed = array_reverse($productSaleItems);
            $snapshots = [];

            foreach ($reversed as $item) {
                $after  = $runningQty;
                $before = $runningQty + $item->qty; // antes da saída
                $snapshots[$item->item_id] = [$before, $after];
                $runningQty = $before;
            }

            foreach ($productSaleItems as $item) {
                [$before, $after] = $snapshots[$item->item_id];

                $rows[] = [
                    'product_id'      => $item->product_id,
                    'company_id'      => $item->company_id,
                    'user_id'         => null,
                    'type'            => 'saida',
                    'quantity'        => -$item->qty,
                    'quantity_before' => $before,
                    'quantity_after'  => $after,
                    'reason'          => 'venda',
                    'notes'           => 'Venda #' . $item->sale_id
                                       . ($item->customer_name ? ' — ' . $item->customer_name : ''),
                    'source_type'     => 'App\\Models\\Sale',
                    'source_id'       => $item->sale_id,
                    'created_at'      => $item->sale_created_at,
                    'updated_at'      => $item->sale_created_at,
                ];
            }
        }

        // Insere em lotes de 200 para não estourar memória em bases grandes
        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('stock_movements')->insert($chunk);
        }
    }

    public function down(): void
    {
        // Remove apenas os movimentos gerados por esta migration retroativa
        DB::table('stock_movements')
            ->where('reason', 'venda')
            ->whereNull('user_id')
            ->whereNotNull('source_id')
            ->delete();
    }
};
