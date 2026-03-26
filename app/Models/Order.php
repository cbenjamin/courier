<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'subscription_id',
    'type',
    'status',
    'items',
    'delivery_address',
    'delivery_city',
    'delivery_state',
    'delivery_zip',
    'scheduled_at',
    'stripe_payment_intent_id',
    'amount_cents',
    'notes',
])]
class Order extends Model
{
    const TYPE_ADHOC = 'adhoc';
    const TYPE_SUBSCRIPTION = 'subscription';

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PICKED_UP = 'picked_up';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PICKED_UP,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'scheduled_at' => 'datetime',
            'amount_cents' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getAmountFormattedAttribute(): string
    {
        return $this->amount_cents ? '$'.number_format($this->amount_cents / 100, 2) : 'N/A';
    }
}
