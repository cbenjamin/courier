@extends('layouts.admin')

@section('title', $user->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Users</a>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="col-span-2 space-y-6">
        <!-- User Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400 mt-1">Joined {{ $user->created_at->format('M j, Y') }}</p>
                </div>
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PATCH')
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="hidden" name="is_admin" value="0">
                        <input type="checkbox" name="is_admin" value="1" onchange="this.form.submit()"
                            {{ $user->is_admin ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-600">
                        Admin role
                    </label>
                </form>
            </div>

            @if($user->profile)
                <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-gray-600">
                    @if($user->profile->phone) <p>{{ $user->profile->phone }}</p> @endif
                    @if($user->profile->address)
                        <p>{{ $user->profile->address }}, {{ $user->profile->city }}, {{ $user->profile->state }} {{ $user->profile->zip }}</p>
                    @endif
                    @if($user->profile->notes) <p class="text-gray-400 mt-1">{{ $user->profile->notes }}</p> @endif
                </div>
            @endif
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Orders</h3>
            </div>
            @forelse($user->orders as $order)
                <a href="{{ route('admin.orders.show', $order) }}"
                    class="flex items-center justify-between px-6 py-3 border-b border-gray-50 hover:bg-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900">#{{ $order->id }}
                            <span class="text-xs font-normal text-gray-400 uppercase ml-1">{{ $order->type }}</span>
                        </p>
                        <p class="text-xs text-gray-500">{{ $order->scheduled_at?->format('M j, Y') }}</p>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                        @if($order->status === 'delivered') bg-green-100 text-green-700
                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                        @elseif($order->status === 'picked_up') bg-yellow-100 text-yellow-700
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </a>
            @empty
                <p class="px-6 py-4 text-sm text-gray-400">No orders yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Subscription sidebar -->
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Subscription</h3>
            @if($user->subscription)
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $user->subscription->status)) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Orders used</dt>
                        <dd class="font-medium text-gray-900">{{ $user->subscription->orders_used }} / 4</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Period ends</dt>
                        <dd class="font-medium text-gray-900">{{ $user->subscription->period_end?->format('M j, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-gray-400">No subscription</p>
            @endif
        </div>
    </div>
</div>
@endsection
