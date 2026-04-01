@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="max-w-2xl">

    @include('admin.settings._subnav')

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Delivery Blackout Dates</h2>
            <p class="text-sm text-gray-500 mt-1">Customers cannot schedule a delivery on these dates.</p>
        </div>

        @forelse($blackouts as $blackout)
            <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $blackout->date->format('D, M j, Y') }}</p>
                    @if($blackout->reason)
                        <p class="text-xs text-gray-400">{{ $blackout->reason }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.blackouts.destroy', $blackout) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors">Remove</button>
                </form>
            </div>
        @empty
            <p class="px-6 py-4 text-sm text-gray-400">No blackout dates set.</p>
        @endforelse

        <div class="px-6 py-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            <p class="text-sm font-medium text-gray-700 mb-3">Add a blackout date</p>
            <form method="POST" action="{{ route('admin.settings.blackouts.store') }}" class="flex gap-3 flex-wrap">
                @csrf
                <div class="flex-1 min-w-36">
                    <input type="date" name="date" min="{{ now()->toDateString() }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('date') border-red-400 @enderror">
                    @error('date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1 min-w-48">
                    <input type="text" name="reason" placeholder="Reason (optional)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors whitespace-nowrap">
                    Add Date
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
