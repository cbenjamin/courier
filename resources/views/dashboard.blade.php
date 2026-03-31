@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Welcome back, {{ $user->name }}</h1>
    <p class="text-gray-500 mt-1">Manage your Whole Foods deliveries</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Subscription Status -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500 mb-1">Subscription</p>
        @if($user->subscription?->isActive())
            <p class="text-xl font-semibold text-brand-700">Active</p>
            <p class="text-sm text-gray-500 mt-1">
                {{ $user->subscription->orders_used }} / 4 deliveries used this month
            </p>
            @if($user->subscription->status === 'cancelling')
                <p class="text-xs text-orange-500 mt-1">Cancels {{ $user->subscription->period_end?->format('M j') }}</p>
            @elseif($user->subscription->period_end)
                <p class="text-xs text-gray-400 mt-1">Renews {{ $user->subscription->period_end->format('M j') }}</p>
            @endif
            <a href="{{ route('subscribe.show') }}" class="text-sm text-brand-600 hover:underline mt-2 inline-block">
                Manage subscription →
            </a>
        @else
            <p class="text-xl font-semibold text-gray-400">No Plan</p>
            <a href="{{ route('subscribe.show') }}" class="text-sm text-brand-600 hover:underline mt-1 inline-block">
                Subscribe for discounted deliveries →
            </a>
        @endif
    </div>

    <!-- Quick Action -->
    <div class="bg-brand-600 rounded-2xl p-6 text-white">
        <p class="text-sm font-medium text-brand-200 mb-1">Ready for a delivery?</p>
        @if($user->subscription?->isActive() && $user->subscription->hasCreditsRemaining())
            <p class="text-lg font-semibold mb-3">{{ 4 - $user->subscription->orders_used }} subscription credits left</p>
        @else
            <p class="text-lg font-semibold mb-3">Request a Whole Foods pickup</p>
        @endif
        <a href="{{ route('orders.create') }}"
            class="inline-block bg-white text-brand-700 font-medium px-4 py-2 rounded-lg text-sm hover:bg-brand-50 transition-colors">
            Request Delivery
        </a>
    </div>

    <!-- Total Deliveries -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500 mb-1">Total Deliveries</p>
        <p class="text-xl font-semibold text-gray-900">{{ $totalOrders }}</p>
        <a href="{{ route('orders.index') }}" class="text-sm text-brand-600 hover:underline mt-1 inline-block">
            View all deliveries →
        </a>
    </div>
</div>

<!-- Recent Deliveries -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">Recent Deliveries</h2>
        <a href="{{ route('orders.index') }}" class="text-sm text-brand-600 hover:underline">View all</a>
    </div>

    @forelse($recentOrders as $order)
        <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition-colors last:border-0">
            <div>
                <p class="text-sm font-medium text-gray-900">
                    Delivery #{{ $order->id }}
                    <span class="ml-2 text-xs text-gray-400 uppercase tracking-wide">{{ $order->type }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                    Pickup {{ $order->pickup_time?->format('M j, Y g:i A') ?? 'time TBD' }}
                </p>
            </div>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full
                @if($order->status === 'delivered') bg-green-100 text-green-700
                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                @elseif($order->status === 'picked_up') bg-yellow-100 text-yellow-700
                @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                @else bg-gray-100 text-gray-600 @endif">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </a>
    @empty
        <div class="px-6 py-10 text-center text-gray-400">
            <p class="text-sm">No deliveries yet.</p>
            <a href="{{ route('orders.create') }}" class="text-brand-600 text-sm hover:underline mt-1 inline-block">Request your first delivery</a>
        </div>
    @endforelse
</div>
@endsection
