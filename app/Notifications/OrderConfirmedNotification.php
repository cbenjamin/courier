<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject('Your Order is Confirmed!')
            ->greeting("Hello {$notifiable->name},")
            ->line('Great news — your courier order has been confirmed.')
            ->line('We will pick up your items from Whole Foods and deliver them to you on the scheduled date.')
            ->action('View Order', route('orders.show', $this->order))
            ->line('Thank you for using our courier service!');
    }
}
