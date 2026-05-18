<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    public function __construct(public readonly Product $product) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'low_stock',
            'product_id' => $this->product->id,
            'product'    => $this->product->name,
            'quantity'   => $this->product->quantity,
            'min'        => $this->product->min_quantity,
            'message'    => "Estoque baixo: {$this->product->name} ({$this->product->quantity} un.)",
            'url'        => route('products.show', $this->product),
        ];
    }
}
