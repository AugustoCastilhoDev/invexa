<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class SyncSubscriptions extends Command
{
    protected $signature   = 'invexa:sync-subscriptions {--company= : ID específico da empresa}';
    protected $description = 'Sincroniza o campo plan de todas as empresas com o status real da assinatura no Stripe.';

    public function handle(): void
    {
        $query = Company::whereNotNull('stripe_id');

        if ($id = $this->option('company')) {
            $query->where('id', $id);
        }

        $companies = $query->get();
        $this->info('Sincronizando ' . $companies->count() . ' empresa(s)...');
        $bar = $this->output->createProgressBar($companies->count());
        $bar->start();

        foreach ($companies as $company) {
            try {
                $company->syncPlanFromSubscription();
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn('Erro na empresa #' . $company->id . ': ' . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sincronização concluída.');
    }
}
