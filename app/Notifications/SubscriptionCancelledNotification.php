<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $periodEnd = $this->subscription->period_end?->format('M j, Y');

        return (new MailMessage)
            ->subject('Subscription Cancelled — Wiregrass Courier')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your Wiregrass Courier monthly subscription has been cancelled.")
            ->when($periodEnd, fn ($mail) => $mail->line(
                "You'll continue to have full access to your subscription benefits through **{$periodEnd}**."
            ))
            ->line('If you change your mind, you can reactivate your subscription any time before that date.')
            ->action('Manage Subscription', route('subscribe.show'))
            ->line('Thank you for being a Wiregrass Courier customer.');
    }
}
