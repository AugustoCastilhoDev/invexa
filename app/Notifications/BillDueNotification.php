<?php

namespace App\Notifications;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillDueNotification extends Notification
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
            'type'    => 'bill_due',
            'title'   => 'Conta a pagar vencendo',
            'message' => "A conta \"{$this->bill->description}\" de R$ " . number_format($this->bill->amount, 2, ',', '.') . " vence em " . Carbon::parse($this->bill->due_date)->format('d/m/Y') . ".",
            'url'     => route('bills.index'),
            'bill_id' => $this->bill->id,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $valor    = 'R$ ' . number_format($this->bill->amount, 2, ',', '.');
        $vencimento = Carbon::parse($this->bill->due_date)->format('d/m/Y');

        return (new MailMessage)
            ->subject('⚠️ Conta a pagar vencendo em breve — ' . $this->bill->description)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('A conta **' . $this->bill->description . '** está vencendo em breve.')
            ->line('**Valor:** ' . $valor)
            ->line('**Vencimento:** ' . $vencimento)
            ->action('Ver contas a pagar', route('bills.index'))
            ->line('Mantenha suas finanças em dia com o Invexa.');
    }
}
