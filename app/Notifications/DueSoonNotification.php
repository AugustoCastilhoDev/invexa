<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DueSoonNotification extends Notification
{
    public function __construct(
        public readonly string $type,   // 'bill' | 'receivable'
        public readonly int    $id,
        public readonly string $description,
        public readonly float  $amount,
        public readonly string $dueDate,
    ) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toArray(object $notifiable): array
    {
        $route = $this->type === 'bill'
            ? route('bills.show', $this->id)
            : route('receivables.show', $this->id);

        $label = $this->type === 'bill' ? 'Conta a pagar' : 'Conta a receber';

        return [
            'type'        => 'due_soon',
            'financial'   => $this->type,
            'id'          => $this->id,
            'description' => $this->description,
            'amount'      => $this->amount,
            'due_date'    => $this->dueDate,
            'message'     => "{$label} vence em breve: {$this->description} (R$ ".number_format($this->amount,2,',','.').") — vence {$this->dueDate}",
            'url'         => $route,
        ];
    }
}
