<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FinanceAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $url   = '/bills',
        public string $type  = 'warning',
        public string $icon  = 'bi-wallet2'
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => $this->type,
            'icon'    => $this->icon,
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
        ];
    }
}
