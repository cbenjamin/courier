<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pickup = $this->order->pickup_time?->format('M j, Y g:i A') ?? 'TBD';

        $message = (new MailMessage)
            ->subject('Delivery Request Received — Wiregrass Courier')
            ->greeting("Hi {$notifiable->name},")
            ->line("We've received your delivery request and will have your Whole Foods order picked up and on its way soon.")
            ->line("**Pickup time:** {$pickup}")
            ->line("**Deliver to:** {$this->order->delivery_address}, {$this->order->delivery_city}, {$this->order->delivery_state} {$this->order->delivery_zip}");

        if ($this->order->type === 'subscription') {
            $remaining = max(0, 4 - $this->order->subscription?->orders_used);
            $message->line("**Subscription credits remaining this month:** {$remaining}");
        }

        return $message
            ->action('View Delivery', route('orders.show', $this->order))
            ->line("We'll be in touch once we've picked up your order.");
    }
}
