@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<!-- Filters -->
<div class="flex gap-3 mb-6">
    @foreach(['', 'pending', 'confirmed', 'picked_up', 'delivered', 'cancelled'] as $status)
        <a href="{{ route('admin.orders.index', array_filter(['status' => $status, 'type' => request('type')])) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
            {{ request('status', '') === $status ? 'bg-brand-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            {{ $status ? ucfirst(str_replace('_', ' ', $status)) : 'All' }}
        </a>
    @endforeach
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200">
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
            @forelse($orders as $order)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-brand-600 hover:underline">#{{ $order->id }}</a>
                    </td>
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.users.show', $order->user) }}" class="text-sm text-gray-800 hover:underline">{{ $order->user->name }}</a>
                    </td>
                    <td class="px-6 py-3 text-xs text-gray-400 uppercase">{{ $order->type }}</td>
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
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $orders->links() }}</div>
</div>
@endsection
