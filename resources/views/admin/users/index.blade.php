@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">All Users ({{ $users->total() }})</h2>
    </div>
    <table class="w-full">
        <thead>
            <tr class="text-xs font-medium text-gray-500 uppercase tracking-wide border-b border-gray-100">
                <th class="px-6 py-3 text-left">User</th>
                <th class="px-6 py-3 text-left">Subscription</th>
                <th class="px-6 py-3 text-left">Orders</th>
                <th class="px-6 py-3 text-left">Joined</th>
                <th class="px-6 py-3 text-left">Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-medium text-gray-900 hover:text-brand-600">{{ $user->name }}</a>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                    </td>
                    <td class="px-6 py-3">
                        @if($user->subscription?->isActive())
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-brand-100 text-brand-700">Active</span>
                        @else
                            <span class="text-xs text-gray-400">None</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $user->orders_count }}</td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $user->created_at->format('M j, Y') }}</td>
                    <td class="px-6 py-3">
                        @if($user->is_admin)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Admin</span>
                        @else
                            <span class="text-xs text-gray-400">Customer</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $users->links() }}</div>
</div>
@endsection
