<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CourierNewOrderAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(object $notifiable): string
    {
        $pickup = $this->order->pickup_time?->format('M j g:ia') ?? 'TBD';
        $location = $this->order->pickupLocation?->name ?? 'Unknown';
        $customer = $this->order->user->name;
        $address = "{$this->order->delivery_address}, {$this->order->delivery_city}";

        return "New order #{$this->order->id} — {$customer}. Pickup: {$location} at {$pickup}. Deliver to: {$address}.";
    }
}
