<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'active_subscriptions' => Subscription::whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_CANCELLING])->count(),
            'confirmed_today' => Order::where('status', Order::STATUS_CONFIRMED)
                ->whereDate('updated_at', today())
                ->count(),
            'delivered_today' => Order::where('status', Order::STATUS_DELIVERED)
                ->whereDate('updated_at', today())
                ->count(),
        ];

        $recentOrders = Order::with('user')->latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
