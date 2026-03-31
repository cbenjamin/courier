<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderDeliveredNotification;
use App\Notifications\OrderPickedUpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with('user')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('user', 'payment', 'subscription');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::VALID_STATUSES)],
        ]);

        $previousStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Refund the delivery credit if a subscription order is being cancelled
        if ($request->status === 'cancelled'
            && $previousStatus !== 'cancelled'
            && $order->type === 'subscription'
            && $order->subscription
            && $order->subscription->orders_used > 0
        ) {
            $order->subscription->decrement('orders_used');
        }

        $notification = match ($request->status) {
            'picked_up' => new OrderPickedUpNotification($order),
            'delivered' => new OrderDeliveredNotification($order),
            'cancelled' => new OrderCancelledNotification($order),
            default     => null,
        };

        if ($notification) {
            $order->user->notify($notification);
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated.');
    }
}
