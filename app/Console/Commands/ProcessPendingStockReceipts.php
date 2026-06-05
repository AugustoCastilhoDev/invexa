<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessPendingStockReceipts extends Command
{
    protected $signature   = 'oc:fix-stock {--id= : ID específico da OC}';
    protected $description = 'Reprocessa estoque de OCs recebidas com itens ainda pendentes (quantity_received = 0)';

    public function handle(PurchaseOrderController $controller): int
    {
        $query = PurchaseOrder::with('items')
            ->where('status', 'recebida')
            ->whereHas('items', fn($q) => $q->whereColumn('quantity_received', '<', 'quantity'));

        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('Nenhuma OC com itens pendentes encontrada.');
            return self::SUCCESS;
        }

        foreach ($orders as $order) {
            $this->line("Processando OC #{$order->number} (ID: {$order->id})...");
            DB::transaction(function () use ($order, $controller) {
                $controller->processStockReceipt($order, $order->company_id);
            });
            $this->info("  ✔ Estoque atualizado para OC #{$order->number}");
        }

        $this->info("\nTotal: {$orders->count()} OC(s) reprocessada(s).");
        return self::SUCCESS;
    }
}
