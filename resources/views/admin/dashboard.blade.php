@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500">Pending Deliveries</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending_orders'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500">Active Subscriptions</p>
        <p class="text-3xl font-bold text-brand-600 mt-1">{{ $stats['active_subscriptions'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500">Confirmed Today</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['confirmed_today'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <p class="text-sm font-medium text-gray-500">Delivered Today</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['delivered_today'] }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Recent Deliveries</h2>
    </div>
    <table class="w-full">
        <thead>
            <tr class="text-xs font-medium text-gray-500 uppercase tracking-wide border-b border-gray-100">
                <th class="px-6 py-3 text-left">#</th>
                <th class="px-6 py-3 text-left">Customer</th>
                <th class="px-6 py-3 text-left">Type</th>
                <th class="px-6 py-3 text-left">Pickup Time</th>
                <th class="px-6 py-3 text-left">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-brand-600 hover:underline font-medium">#{{ $order->id }}</a>
                    </td>
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.users.show', $order->user) }}" class="text-sm text-gray-800 hover:underline">{{ $order->user->name }}</a>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500 uppercase">{{ $order->type }}</td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $order->pickup_time?->format('M j, g:i A') ?? '—' }}</td>
                    <td class="px-6 py-3">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            @if($order->status === 'delivered') bg-green-100 text-green-700
                            @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                            @elseif($order->status === 'picked_up') bg-yellow-100 text-yellow-700
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-600 hover:underline">View all deliveries →</a>
    </div>
</div>
@endsection
