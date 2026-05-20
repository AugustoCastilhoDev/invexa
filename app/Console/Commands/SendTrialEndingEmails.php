<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTrialEndingEmails extends Command
{
    protected $signature   = 'invexa:trial-ending-emails';
    protected $description = 'Envia e-mails de aviso para empresas com trial encerrando em 3 ou 1 dia(s)';

    public function handle(): void
    {
        $thresholds = [3, 1];

        foreach ($thresholds as $days) {
            $target = now()->addDays($days)->toDateString();

            $users = User::whereHas('company', fn ($q) =>
                $q->whereDate('trial_ends_at', $target)
                  ->whereNull('stripe_id') // sem assinatura ativa
            )->where('role', 'admin')->get();

            foreach ($users as $user) {
                Mail::to($user->email)->queue(new TrialEndingMail($user));
                $this->line("E-mail enviado para {$user->email} ({$days}d restantes)");
            }
        }

        $this->info('Trial ending emails processados.');
    }
}
