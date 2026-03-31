<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Your Delivery Has Been Cancelled — Wiregrass Courier')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your delivery request (#{$this->order->id}) has been cancelled.");

        if ($this->order->type === 'subscription') {
            $message->line('Your subscription credit for this delivery has been returned to your account.');
        }

        return $message
            ->line('If you have any questions or believe this was a mistake, please reach out to us.')
            ->action('Contact Us', route('contact.show'))
            ->line('We hope to serve you again soon.');
    }
}
