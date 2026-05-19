<?php

namespace App\Notifications;

use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReceivableDueNotification extends Notification
{
    use Queueable;

    public function __construct(public Receivable $receivable) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'          => 'receivable_due',
            'title'         => 'Conta a receber vencendo',
            'message'       => "A conta a receber \"{$this->receivable->description}\" de R$ " . number_format($this->receivable->amount, 2, ',', '.') . " vence em " . Carbon::parse($this->receivable->due_date)->format('d/m/Y') . ".",
            'url'           => route('receivables.index'),
            'receivable_id' => $this->receivable->id,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $valor      = 'R$ ' . number_format($this->receivable->amount, 2, ',', '.');
        $vencimento = Carbon::parse($this->receivable->due_date)->format('d/m/Y');

        return (new MailMessage)
            ->subject('💰 Conta a receber vencendo — ' . $this->receivable->description)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Você tem uma conta a receber com vencimento próximo.')
            ->line('**Descrição:** ' . $this->receivable->description)
            ->line('**Valor:** ' . $valor)
            ->line('**Vencimento:** ' . $vencimento)
            ->action('Ver contas a receber', route('receivables.index'))
            ->line('Acompanhe seus recebimentos pelo Invexa.');
    }
}
