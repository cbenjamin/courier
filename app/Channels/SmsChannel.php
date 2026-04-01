<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! env('SMS_ENABLED', false)) {
            return;
        }

        $phone = $notifiable->routeNotificationFor('sms', $notification);

        if (blank($phone)) {
            return;
        }

        $phone = $this->toE164($phone);

        if ($phone === null) {
            return;
        }

        $message = $notification->toSms($notifiable);

        if (blank($message)) {
            return;
        }

        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));

        $twilio->messages->create($phone, [
            'from' => config('services.twilio.from'),
            'body' => $message,
        ]);
    }

    private function toE164(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 10) {
            return '+1' . $digits;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            return '+' . $digits;
        }

        if (str_starts_with($phone, '+') && strlen($digits) >= 10) {
            return '+' . $digits;
        }

        return null;
    }
}
