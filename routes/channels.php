<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Private channel for order updates (GPS + status)
// Customer can subscribe to their own orders; admin/courier can subscribe to any
Broadcast::channel('order.{orderId}', function (User $user, int $orderId) {
    $order = Order::find($orderId);

    return $order && ($order->user_id === $user->id || $user->is_admin);
});
