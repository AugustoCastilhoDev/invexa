<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'       => 'low_stock',
            'title'      => 'Estoque baixo',
            'message'    => "O produto \"{$this->product->name}\" está com apenas {$this->product->quantity} unidade(s) em estoque.",
            'url'        => route('products.index'),
            'product_id' => $this->product->id,
        ];
    }
}
