<?php

namespace App\Notifications;

use App\Models\Receivable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReceivableDueNotification extends Notification
{
    use Queueable;

    public function __construct(public Receivable $receivable) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'          => 'receivable_due',
            'title'         => 'Conta a receber vencendo',
            'message'       => "O recebível \"{$this->receivable->description}\" de R$ " . number_format($this->receivable->amount, 2, ',', '.') . " vence em " . \Carbon\Carbon::parse($this->receivable->due_date)->format('d/m/Y') . ".",
            'url'           => route('receivables.index'),
            'receivable_id' => $this->receivable->id,
        ];
    }
}
