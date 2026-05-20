<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use App\Notifications\FinanceAlertNotification;
use App\Notifications\StockAlertNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyAlerts extends Command
{
    protected $signature   = 'invexa:daily-alerts';
    protected $description = 'Envia alertas diários de estoque baixo e contas vencendo.';

    public function handle(): void
    {
        Company::all()->each(function (Company $company) {
            $admins = User::where('company_id', $company->id)
                ->whereIn('role', ['admin', 'gerente'])
                ->get();

            if ($admins->isEmpty()) return;

            // ── Estoque baixo / zerado ────────────────────────────────
            $lowStock = Product::where('company_id', $company->id)
                ->whereColumn('quantity', '<=', 'min_quantity')
                ->get();

            foreach ($lowStock as $product) {
                foreach ($admins as $user) {
                    // Evita duplicar se já existe notificação não lida do mesmo produto
                    $already = $user->unreadNotifications
                        ->where('type', 'App\\Notifications\\StockAlertNotification')
                        ->filter(fn($n) => ($n->data['url'] ?? '') === '/products/' . $product->id . '/edit')
                        ->isNotEmpty();

                    if (!$already) {
                        $user->notify(new StockAlertNotification($product));
                    }
                }
            }

            // ── Contas vencendo hoje ou vencidas ─────────────────────
            $today        = now()->toDateString();
            $overdueCount = Bill::where('company_id', $company->id)
                ->where('status', 'vencida')
                ->count();
            $dueTodayCount = Bill::where('company_id', $company->id)
                ->whereIn('status', ['pendente', 'vencida'])
                ->whereDate('due_date', $today)
                ->count();

            if ($overdueCount > 0 || $dueTodayCount > 0) {
                foreach ($admins as $user) {
                    $already = $user->unreadNotifications
                        ->where('type', 'App\\Notifications\\FinanceAlertNotification')
                        ->filter(fn($n) => ($n->data['url'] ?? '') === '/bills')
                        ->isNotEmpty();

                    if (!$already) {
                        $parts = [];
                        if ($overdueCount > 0)  $parts[] = "{$overdueCount} conta(s) vencida(s)";
                        if ($dueTodayCount > 0) $parts[] = "{$dueTodayCount} vence(m) hoje";

                        $user->notify(new FinanceAlertNotification(
                            title:   'Atenção: Contas a Pagar',
                            message: implode(' e ', $parts) . '. Verifique o financeiro.',
                            url:     '/bills',
                            type:    $overdueCount > 0 ? 'danger' : 'warning',
                            icon:    'bi-credit-card-2-front'
                        ));
                    }
                }
            }

            $this->info("[{$company->name}] alertas processados.");
        });
    }
}
