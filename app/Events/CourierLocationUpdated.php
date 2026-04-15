<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourierLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $orderId,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly string $ts,
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('order.'.$this->orderId);
    }

    public function broadcastAs(): string
    {
        return 'courier.location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
            'ts'  => $this->ts,
        ];
    }
}
