<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\User;
use App\Notifications\BillDueNotification;
use App\Notifications\BillOverdueNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\ReceivableDueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckFinancialAlerts extends Command
{
    protected $signature   = 'invexa:check-alerts';
    protected $description = 'Envia notificações de estoque baixo, contas a vencer e contas vencidas';

    public function handle(): void
    {
        $today    = Carbon::today();
        $in3Days  = Carbon::today()->addDays(3);

        // Percorre companies e envia aos usuários admin/gerente de cada empresa
        $companies = \App\Models\Company::all();

        foreach ($companies as $company) {
            $recipients = User::where('company_id', $company->id)
                ->whereIn('role', ['admin', 'gerente'])
                ->where('active', true)
                ->get();

            if ($recipients->isEmpty()) continue;

            // Estoque baixo (min_stock configurável no produto; padrão 5)
            Product::where('company_id', $company->id)
                ->whereRaw('quantity <= COALESCE(min_stock, 5)')
                ->each(function (Product $product) use ($recipients) {
                    $recipients->each(fn($u) => $u->notify(new LowStockNotification($product)));
                });

            // Contas a pagar vencendo em 3 dias
            Bill::where('company_id', $company->id)
                ->where('status', 'pendente')
                ->whereBetween('due_date', [$today, $in3Days])
                ->each(function (Bill $bill) use ($recipients) {
                    $recipients->each(fn($u) => $u->notify(new BillDueNotification($bill)));
                });

            // Contas a pagar vencidas
            Bill::where('company_id', $company->id)
                ->where('status', 'pendente')
                ->where('due_date', '<', $today)
                ->each(function (Bill $bill) use ($recipients) {
                    $recipients->each(fn($u) => $u->notify(new BillOverdueNotification($bill)));
                });

            // Contas a receber vencendo em 3 dias
            Receivable::where('company_id', $company->id)
                ->where('status', 'pendente')
                ->whereBetween('due_date', [$today, $in3Days])
                ->each(function (Receivable $receivable) use ($recipients) {
                    $recipients->each(fn($u) => $u->notify(new ReceivableDueNotification($receivable)));
                });
        }

        $this->info('Alertas verificados e notificações enviadas.');
    }
}
