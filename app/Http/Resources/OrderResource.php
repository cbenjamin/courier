<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'status'          => $this->status,
            'type'            => $this->type,
            'pickup_time'     => $this->pickup_time?->toIso8601String(),
            'pickup_link'     => $this->pickup_link,
            'pickup_location' => $this->whenLoaded('pickupLocation', fn () => [
                'id'      => $this->pickupLocation->id,
                'name'    => $this->pickupLocation->name,
                'address' => $this->pickupLocation->address,
                'city'    => $this->pickupLocation->city,
                'state'   => $this->pickupLocation->state,
                'zip'     => $this->pickupLocation->zip,
            ]),
            'delivery_address' => $this->delivery_address,
            'delivery_city'    => $this->delivery_city,
            'delivery_state'   => $this->delivery_state,
            'delivery_zip'     => $this->delivery_zip,
            'amount_cents'     => $this->amount_cents,
            'tip_cents'        => $this->tip_cents,
            'notes'            => $this->notes,
            'customer'         => $this->whenLoaded('user', fn () => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
