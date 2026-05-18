<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Receivable;
use App\Models\User;
use App\Notifications\DueSoonNotification;
use App\Notifications\OverdueNotification;
use Illuminate\Console\Command;

class CheckFinancialAlerts extends Command
{
    protected $signature   = 'invexa:financial-alerts';
    protected $description = 'Envia notificações de contas vencidas e a vencer nos próximos 3 dias.';

    public function handle(): void
    {
        // Contas a Pagar
        Bill::where('status', 'pendente')
            ->whereDate('due_date', '<=', now()->addDays(3))
            ->each(function (Bill $bill) {
                $users = User::where('company_id', $bill->company_id)
                    ->whereIn('role', ['admin', 'gerente'])->get();

                $dto = [$bill->description, (float)$bill->amount, $bill->due_date->format('d/m/Y')];

                if ($bill->due_date->isPast()) {
                    $users->each(fn($u) => $u->notify(new OverdueNotification('bill', $bill->id, ...$dto)));
                } else {
                    $users->each(fn($u) => $u->notify(new DueSoonNotification('bill', $bill->id, ...$dto)));
                }
            });

        // Contas a Receber
        Receivable::where('status', 'pendente')
            ->whereDate('due_date', '<=', now()->addDays(3))
            ->each(function (Receivable $r) {
                $users = User::where('company_id', $r->company_id)
                    ->whereIn('role', ['admin', 'gerente'])->get();

                $dto = [$r->description, (float)$r->amount, $r->due_date->format('d/m/Y')];

                if ($r->due_date->isPast()) {
                    $users->each(fn($u) => $u->notify(new OverdueNotification('receivable', $r->id, ...$dto)));
                } else {
                    $users->each(fn($u) => $u->notify(new DueSoonNotification('receivable', $r->id, ...$dto)));
                }
            });

        $this->info('Alertas financeiros processados.');
    }
}
