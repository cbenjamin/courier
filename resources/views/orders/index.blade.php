@extends('layouts.app')

@section('title', 'My Deliveries')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">My Deliveries</h1>
    <a href="{{ route('orders.create') }}"
        class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">
        + Request Delivery
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    @forelse($orders as $order)
        <a href="{{ route('orders.show', $order) }}"
            class="flex items-center justify-between px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors last:border-0">
            <div>
                <p class="text-sm font-medium text-gray-900">
                    Delivery #{{ $order->id }}
                    <span class="ml-2 text-xs font-normal text-gray-400 uppercase tracking-wide">{{ $order->type }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                    Pickup {{ $order->pickup_time?->format('M j, Y g:i A') ?? 'time TBD' }}
                    @if($order->amount_cents)
                        &middot; {{ $order->amount_formatted }}
                    @endif
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
        <div class="px-6 py-16 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="font-medium text-gray-500">No deliveries yet</p>
            <a href="{{ route('orders.create') }}" class="text-brand-600 text-sm hover:underline mt-1 inline-block">
                Request your first delivery
            </a>
        </div>
    @endforelse
</div>

@if($orders->hasPages())
    <div class="mt-6">{{ $orders->links() }}</div>
@endif
@endsection
