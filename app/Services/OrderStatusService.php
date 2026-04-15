<?php

namespace App\Services;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderDeliveredNotification;
use App\Notifications\OrderPickedUpNotification;

class OrderStatusService
{
    public function update(Order $order, string $newStatus): Order
    {
        $previousStatus = $order->status;

        $order->update(['status' => $newStatus]);

        // Refund the delivery credit if a subscription order is being cancelled
        if ($newStatus === Order::STATUS_CANCELLED
            && $previousStatus !== Order::STATUS_CANCELLED
            && $order->type === Order::TYPE_SUBSCRIPTION
            && $order->subscription
            && $order->subscription->orders_used > 0
        ) {
            $order->subscription->decrement('orders_used');
        }

        $notification = match ($newStatus) {
            Order::STATUS_PICKED_UP  => new OrderPickedUpNotification($order),
            Order::STATUS_DELIVERED  => new OrderDeliveredNotification($order),
            Order::STATUS_CANCELLED  => new OrderCancelledNotification($order),
            default                  => null,
        };

        if ($notification) {
            $order->user->notify($notification);
        }

        event(new OrderStatusUpdated($order));

        return $order->fresh();
    }
}
