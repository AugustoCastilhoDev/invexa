<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $critical = $this->product->quantity <= 0;
        return [
            'type'    => $critical ? 'danger' : 'warning',
            'icon'    => $critical ? 'bi-exclamation-octagon-fill' : 'bi-exclamation-triangle-fill',
            'title'   => $critical ? 'Produto sem estoque!' : 'Estoque baixo',
            'message' => $critical
                ? "{$this->product->name} está com estoque zerado."
                : "{$this->product->name} tem apenas {$this->product->quantity} unidade(s) restante(s).",
            'url'     => '/products/' . $this->product->id . '/edit',
        ];
    }
}
