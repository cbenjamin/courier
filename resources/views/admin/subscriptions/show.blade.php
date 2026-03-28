@extends('layouts.admin')

@section('title', 'Subscription — ' . $subscription->user->name)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('admin.subscriptions.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Subscriptions</a>
        <h1 class="text-xl font-semibold text-gray-900 mt-1">{{ $subscription->user->name }}'s Subscription</h1>
    </div>

    @if($subscription->isActive())
        <div x-data="{ open: false }">
            <button @click="open = true"
                class="bg-red-50 hover:bg-red-100 text-red-600 font-medium px-4 py-2 rounded-lg text-sm border border-red-200 transition-colors">
                Cancel Subscription
            </button>

            <!-- Confirmation modal -->
            <div x-show="open" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                @keydown.escape.window="open = false">
                <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full mx-4" @click.stop>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Cancel immediately?</h3>
                    <p class="text-sm text-gray-500 mb-1">
                        This will cancel <strong>{{ $subscription->user->name }}</strong>'s subscription in Stripe right now.
                    </p>
                    <p class="text-sm text-red-500 mb-6">They will lose access immediately and will not be charged again.</p>
                    <div class="flex gap-3">
                        <button @click="open = false"
                            class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 rounded-lg text-sm transition-colors">
                            Go back
                        </button>
                        <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription) }}" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg text-sm transition-colors">
                                Cancel now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">

        <!-- Status card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Subscription Details</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</dt>
                    <dd>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            @if($subscription->status === 'active') bg-green-100 text-green-700
                            @elseif($subscription->status === 'cancelling') bg-orange-100 text-orange-700
                            @elseif($subscription->status === 'past_due') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Orders Used</dt>
                    <dd class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">{{ $subscription->orders_used }} / 4</span>
                        <div class="w-20 bg-gray-200 rounded-full h-1.5">
                            <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ ($subscription->orders_used / 4) * 100 }}%"></div>
                        </div>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Period Start</dt>
                    <dd class="text-gray-800">{{ $subscription->period_start?->format('M j, Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">
                        {{ $subscription->status === 'cancelling' ? 'Cancels On' : 'Renews On' }}
                    </dt>
                    <dd class="text-gray-800">{{ $subscription->period_end?->format('M j, Y') ?? '—' }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Stripe Subscription ID</dt>
                    <dd class="font-mono text-xs text-gray-500">{{ $subscription->stripe_subscription_id }}</dd>
                </div>
            </dl>
        </div>

        <!-- Orders under this subscription -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Orders on this subscription</h2>
            </div>
            @forelse($subscription->orders as $order)
                <a href="{{ route('admin.orders.show', $order) }}"
                    class="flex items-center justify-between px-6 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Order #{{ $order->id }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Pickup {{ $order->pickup_time?->format('M j, Y g:i A') ?? 'TBD' }}</p>
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
                <p class="px-6 py-8 text-sm text-center text-gray-400">No orders placed on this subscription.</p>
            @endforelse
        </div>

    </div>

    <!-- Sidebar: customer info -->
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Customer</h3>
            <a href="{{ route('admin.users.show', $subscription->user) }}"
                class="text-sm font-medium text-brand-600 hover:underline">
                {{ $subscription->user->name }}
            </a>
            <p class="text-xs text-gray-500 mt-0.5">{{ $subscription->user->email }}</p>
            @if($subscription->user->profile?->phone)
                <p class="text-xs text-gray-500 mt-0.5">{{ $subscription->user->profile->phone }}</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-2">Subscription started</h3>
            <p class="text-sm text-gray-600">{{ $subscription->created_at->format('M j, Y') }}</p>
        </div>
    </div>
</div>
@endsection
