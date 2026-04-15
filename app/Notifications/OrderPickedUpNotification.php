<?php

namespace App\Notifications;

use App\Channels\ExpoPushChannel;
use App\Channels\SmsChannel;
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
        return ['mail', SmsChannel::class, ExpoPushChannel::class];
    }

    public function toExpoPush(object $notifiable): array
    {
        return [
            'title' => 'Order Picked Up',
            'body'  => 'Your order is on the way! Open the app to track your delivery.',
            'data'  => [
                'order_id' => $this->order->id,
                'screen'   => 'order-detail',
            ],
        ];
    }

    public function toSms(object $notifiable): string
    {
        $address = "{$this->order->delivery_address}, {$this->order->delivery_city}";

        return "Wiregrass Courier: Your order has been picked up and is on the way to {$address}.";
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
