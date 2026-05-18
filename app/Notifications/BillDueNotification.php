<?php

namespace App\Notifications;

use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BillDueNotification extends Notification
{
    use Queueable;

    public function __construct(public Bill $bill) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'bill_due',
            'title'   => 'Conta a pagar vencendo',
            'message' => "A conta \"{$this->bill->description}\" de R$ " . number_format($this->bill->amount, 2, ',', '.') . " vence em " . \Carbon\Carbon::parse($this->bill->due_date)->format('d/m/Y') . ".",
            'url'     => route('bills.index'),
            'bill_id' => $this->bill->id,
        ];
    }
}
