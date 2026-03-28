@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Orders</a>
</div>

<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">
        <!-- Pickup Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Whole Foods Pickup</h2>
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Pickup Link</dt>
                    <dd>
                        <a href="{{ $order->pickup_link }}" target="_blank" rel="noopener noreferrer"
                            class="text-brand-600 hover:underline break-all">
                            {{ $order->pickup_link }}
                        </a>
                    </dd>
                </div>
                @if($order->pickup_time)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Pickup Time</dt>
                        <dd class="text-gray-800">{{ $order->pickup_time->format('M j, Y g:i A') }}</dd>
                    </div>
                @endif
            </dl>
            @if($order->notes)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Notes</p>
                    <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Delivery -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Delivery Address</h2>
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-gray-500 text-xs uppercase tracking-wide">Address</dt>
                    <dd class="text-gray-800 mt-0.5">
                        {{ $order->delivery_address }}<br>
                        {{ $order->delivery_city }}, {{ $order->delivery_state }} {{ $order->delivery_zip }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-4">
        <!-- Status Update -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Update Status</h3>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 mb-3">
                    @foreach(\App\Models\Order::VALID_STATUSES as $status)
                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Customer -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Customer</h3>
            <a href="{{ route('admin.users.show', $order->user) }}" class="text-sm text-brand-600 hover:underline font-medium">{{ $order->user->name }}</a>
            <p class="text-xs text-gray-500 mt-0.5">{{ $order->user->email }}</p>
        </div>

        <!-- Payment -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Payment</h3>
            @if($order->type === 'subscription')
                <p class="text-sm text-brand-700 font-medium">Subscription order</p>
            @elseif($order->payment)
                <p class="text-sm font-semibold text-gray-800">{{ $order->amount_formatted }}</p>
                <p class="text-xs text-green-600 mt-1">Paid {{ $order->payment->paid_at?->format('M j, Y') }}</p>
            @else
                <p class="text-sm text-gray-500">{{ $order->amount_formatted }}</p>
                <p class="text-xs text-yellow-600 mt-1">Payment pending</p>
            @endif
        </div>
    </div>
</div>
@endsection
