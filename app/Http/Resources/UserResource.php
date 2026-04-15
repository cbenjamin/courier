<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'is_admin'     => $this->is_admin,
            'profile'      => $this->whenLoaded('profile', fn () => [
                'phone'           => $this->profile?->phone,
                'address'         => $this->profile?->address,
                'city'            => $this->profile?->city,
                'state'           => $this->profile?->state,
                'zip'             => $this->profile?->zip,
                'notes'           => $this->profile?->notes,
                'expo_push_token' => $this->profile?->expo_push_token,
            ]),
            'subscription' => $this->whenLoaded('subscription', fn () => $this->subscription ? [
                'status'           => $this->subscription->status,
                'orders_used'      => $this->subscription->orders_used,
                'orders_remaining' => max(0, 4 - $this->subscription->orders_used),
                'period_end'       => $this->subscription->period_end?->toIso8601String(),
            ] : null),
        ];
    }
}
