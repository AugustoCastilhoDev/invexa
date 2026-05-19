<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
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

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('📦 Estoque baixo — ' . $this->product->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O produto abaixo está com estoque abaixo do mínimo configurado.')
            ->line('**Produto:** ' . $this->product->name)
            ->line('**Quantidade atual:** ' . $this->product->quantity . ' unidade(s)')
            ->line('**Estoque mínimo:** ' . ($this->product->min_stock ?? 5) . ' unidade(s)')
            ->action('Ver produtos', route('products.index'))
            ->line('Faça um pedido de reposição para evitar rupturas de estoque.');
    }
}
