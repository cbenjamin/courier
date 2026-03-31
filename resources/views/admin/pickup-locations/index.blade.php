@extends('layouts.admin')

@section('title', 'Pickup Locations')

@section('content')
<div class="max-w-3xl space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Add Location -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-1">Add Pickup Location</h2>
        <p class="text-sm text-gray-500 mb-5">Add grocery store locations your couriers can pick up from. Customers will search and select from these when placing an order.</p>

        <form method="POST" action="{{ route('admin.pickup-locations.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="e.g. Whole Foods"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        placeholder="123 Main St"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('address') border-red-400 @enderror">
                    @error('address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('city') border-red-400 @enderror">
                    @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input type="text" name="state" value="{{ old('state', 'AL') }}"
                        maxlength="2" placeholder="AL"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 uppercase @error('state') border-red-400 @enderror">
                    @error('state') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ZIP</label>
                    <input type="text" name="zip" value="{{ old('zip') }}"
                        maxlength="10"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('zip') border-red-400 @enderror">
                    @error('zip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                    Add Location
                </button>
            </div>
        </form>
    </div>

    <!-- Location List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">All Locations <span class="text-gray-400 font-normal text-sm">({{ $locations->count() }})</span></h2>
        </div>

        @forelse($locations as $location)
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50 last:border-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-900">{{ $location->name }}</p>
                        @if(!$location->active)
                            <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full">Inactive</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $location->full_address }}</p>
                </div>
                <div class="flex items-center gap-3 ml-4 flex-shrink-0">
                    <a href="{{ route('admin.pickup-locations.edit', $location) }}"
                        class="text-sm text-brand-600 hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.pickup-locations.destroy', $location) }}"
                        onsubmit="return confirm('Remove {{ addslashes($location->name) }} in {{ addslashes($location->city) }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-500 hover:text-red-700">Remove</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-6 py-10 text-center text-gray-400">
                <p class="text-sm">No pickup locations yet. Add one above.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
