<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Estende o trial de 14 para 30 dias para todas as empresas
 * que se cadastraram antes da alteração (trial_ends_at baseado em 14 dias)
 * e que ainda estão em trial ativo (plano free, sem assinatura paga).
 *
 * Lógica:
 *  - Somente empresas com plano = 'free'
 *  - Que não possuem assinatura ativa na tabela subscriptions
 *  - O novo trial_ends_at = created_at + 30 dias
 *  - Não retrocede quem já tiver um trial_ends_at maior (ex: ajustes manuais)
 */
return new class extends Migration
{
    public function up(): void
    {
        // IDs de empresas que já têm assinatura ativa (não devem ser tocadas)
        $withSubscription = DB::table('subscriptions')
            ->whereNotNull('ends_at')
            ->orWhereNull('ends_at')
            ->pluck('billable_id')
            ->toArray();

        DB::table('companies')
            ->where('plan', 'free')
            ->whereNotIn('id', $withSubscription)
            ->whereNotNull('trial_ends_at')
            ->orderBy('id')
            ->chunkById(100, function ($companies) {
                foreach ($companies as $company) {
                    $newTrialEndsAt = \Illuminate\Support\Carbon::parse($company->created_at)
                        ->addDays(30);

                    // Só atualiza se o novo valor for MAIOR que o atual
                    // (evita regredir quem já tiver trial estendido manualmente)
                    if ($newTrialEndsAt->greaterThan(
                        \Illuminate\Support\Carbon::parse($company->trial_ends_at)
                    )) {
                        DB::table('companies')
                            ->where('id', $company->id)
                            ->update(['trial_ends_at' => $newTrialEndsAt]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Reverte: trial_ends_at = created_at + 14 dias
        // (somente empresas free sem assinatura)
        $withSubscription = DB::table('subscriptions')
            ->pluck('billable_id')
            ->toArray();

        DB::table('companies')
            ->where('plan', 'free')
            ->whereNotIn('id', $withSubscription)
            ->whereNotNull('trial_ends_at')
            ->orderBy('id')
            ->chunkById(100, function ($companies) {
                foreach ($companies as $company) {
                    DB::table('companies')
                        ->where('id', $company->id)
                        ->update([
                            'trial_ends_at' => \Illuminate\Support\Carbon::parse($company->created_at)
                                ->addDays(14),
                        ]);
                }
            });
    }
};
