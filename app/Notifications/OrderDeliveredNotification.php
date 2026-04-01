<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDeliveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', SmsChannel::class];
    }

    public function toSms(object $notifiable): string
    {
        return 'Wiregrass Courier: Your order has been delivered! Thank you for choosing us.';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Order Has Been Delivered — Wiregrass Courier')
            ->greeting("Hi {$notifiable->name},")
            ->line('Your Whole Foods order has been delivered. Enjoy!')
            ->action('View Delivery', route('orders.show', $this->order))
            ->line('If your courier went above and beyond, consider leaving them a tip — it goes a long way!')
            ->action('Leave a Tip', route('orders.tip', $this->order))
            ->line('Thank you for choosing Wiregrass Courier. We look forward to serving you again!');
    }
}
