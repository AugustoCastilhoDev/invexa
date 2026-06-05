<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification
{
    public function __construct(public Company $company) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Falha no pagamento da assinatura Invexa')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Houve uma falha ao processar o pagamento da assinatura da empresa **' . $this->company->name . '**.')
            ->action('Atualizar forma de pagamento', route('subscription.billing-portal'))
            ->line('Se não atualizar em 3 dias, o acesso será suspenso.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'payment_failed',
            'message' => 'Falha no pagamento da assinatura. Atualize sua forma de pagamento.',
            'url'     => route('subscription.billing-portal'),
        ];
    }
}
