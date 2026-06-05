<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionTrialEnding extends Notification
{
    public function __construct(public Company $company) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⏳ Seu trial do Invexa termina em 3 dias')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O período de teste da empresa **' . $this->company->name . '** se encerra em **3 dias**.')
            ->line('Assine agora para continuar usando todos os recursos sem interrupção.')
            ->action('Ver planos', route('pricing'))
            ->line('Após o término do trial, a conta passará automaticamente para o plano gratuito com recursos limitados.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'trial_ending',
            'message' => 'Seu trial termina em 3 dias. Assine agora para não perder o acesso.',
            'url'     => route('pricing'),
        ];
    }
}
