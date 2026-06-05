<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class ExpireTrials extends Command
{
    protected $signature   = 'invexa:expire-trials';
    protected $description = 'Rebaixa para free empresas cujo trial expirou e não possuem assinatura ativa.';

    public function handle(): void
    {
        $expired = Company::where('trial_ends_at', '<', now())
            ->where('plan', 'free')
            ->whereNull('stripe_id')
            ->orWhere(function ($q) {
                $q->where('trial_ends_at', '<', now())
                  ->whereNotNull('stripe_id')
                  ->where('plan', 'free');
            })
            ->get()
            ->filter(fn ($c) => ! $c->hasActiveSubscription());

        $count = 0;
        foreach ($expired as $company) {
            $company->update(['trial_ends_at' => null]);
            $count++;
        }

        $this->info("$count empresa(s) com trial expirado processada(s).");
    }
}
