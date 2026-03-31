<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPickedUpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Order Has Been Picked Up — Wiregrass Courier')
            ->greeting("Hi {$notifiable->name},")
            ->line("Great news! We've picked up your Whole Foods order and are heading your way.")
            ->line("**Delivering to:** {$this->order->delivery_address}, {$this->order->delivery_city}, {$this->order->delivery_state} {$this->order->delivery_zip}")
            ->action('View Delivery', route('orders.show', $this->order))
            ->line('Please be available to receive your delivery.');
    }
}
