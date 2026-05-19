<?php

namespace App\Notifications;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillOverdueNotification extends Notification
{
    use Queueable;

    public function __construct(public Bill $bill) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'bill_overdue',
            'title'   => 'Conta a pagar vencida',
            'message' => "A conta \"{$this->bill->description}\" de R$ " . number_format($this->bill->amount, 2, ',', '.') . " venceu em " . Carbon::parse($this->bill->due_date)->format('d/m/Y') . " e ainda está pendente.",
            'url'     => route('bills.index'),
            'bill_id' => $this->bill->id,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $valor      = 'R$ ' . number_format($this->bill->amount, 2, ',', '.');
        $vencimento = Carbon::parse($this->bill->due_date)->format('d/m/Y');

        return (new MailMessage)
            ->subject('🔴 Conta VENCIDA — ' . $this->bill->description)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('A conta abaixo está **vencida** e ainda não foi paga.')
            ->line('**Conta:** ' . $this->bill->description)
            ->line('**Valor:** ' . $valor)
            ->line('**Venceu em:** ' . $vencimento)
            ->action('Regularizar agora', route('bills.index'))
            ->line('Regularize o quanto antes para evitar juros e problemas.');
    }
}
