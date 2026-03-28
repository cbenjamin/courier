@extends('layouts.admin')

@section('title', 'Subscriptions')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">All Subscriptions ({{ $subscriptions->total() }})</h2>
    </div>
    <table class="w-full">
        <thead>
            <tr class="text-xs font-medium text-gray-500 uppercase tracking-wide border-b border-gray-100">
                <th class="px-6 py-3 text-left">Customer</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Orders Used</th>
                <th class="px-6 py-3 text-left">Period End</th>
                <th class="px-6 py-3 text-left">Started</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $sub)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer"
                    onclick="window.location='{{ route('admin.subscriptions.show', $sub) }}'">
                    <td class="px-6 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $sub->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $sub->user->email }}</p>
                    </td>
                    <td class="px-6 py-3">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            @if($sub->status === 'active') bg-green-100 text-green-700
                            @elseif($sub->status === 'cancelling') bg-orange-100 text-orange-700
                            @elseif($sub->status === 'past_due') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-800">{{ $sub->orders_used }} / 4</span>
                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ ($sub->orders_used / 4) * 100 }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $sub->period_end?->format('M j, Y') ?? '—' }}</td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $sub->created_at->format('M j, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">No subscriptions found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $subscriptions->links() }}</div>
</div>
@endsection
