<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'picked_up' => 'Picked Up from Whole Foods',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        $label = $statusLabels[$this->order->status] ?? ucfirst($this->order->status);

        return (new MailMessage)
            ->subject("Order Update: {$label}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your order status has been updated to: **{$label}**.")
            ->action('View Order', route('orders.show', $this->order))
            ->line('Thank you for using our courier service!');
    }
}
