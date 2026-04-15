<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushChannel
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    public function send(object $notifiable, Notification $notification): void
    {
        $token = $notifiable->profile?->expo_push_token;

        if (blank($token)) {
            return;
        }

        $payload = $notification->toExpoPush($notifiable);

        if (blank($payload)) {
            return;
        }

        $response = Http::post(self::EXPO_PUSH_URL, [
            'to'    => $token,
            'title' => $payload['title'] ?? null,
            'body'  => $payload['body'] ?? null,
            'data'  => $payload['data'] ?? [],
            'sound' => 'default',
        ]);

        if ($response->failed()) {
            Log::warning('Expo push notification failed', [
                'token'    => $token,
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
        }
    }
}
