<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Your Monthly Subscription!')
            ->greeting("Hello {$notifiable->name},")
            ->line('Your monthly courier subscription is now active.')
            ->line('You can place up to 4 orders per month at a discounted rate.')
            ->action('Place Your First Order', route('orders.create'))
            ->line('Thank you for subscribing!');
    }
}
