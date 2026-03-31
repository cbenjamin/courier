@extends('layouts.admin')

@section('title', 'Edit Pickup Location')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('admin.pickup-locations.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Pickup Locations</a>
        <h1 class="text-xl font-semibold text-gray-900 mt-2">Edit Location</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.pickup-locations.update', $pickupLocation) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                    <input type="text" name="name" value="{{ old('name', $pickupLocation->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                    <input type="text" name="address" value="{{ old('address', $pickupLocation->address) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('address') border-red-400 @enderror">
                    @error('address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', $pickupLocation->city) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('city') border-red-400 @enderror">
                    @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input type="text" name="state" value="{{ old('state', $pickupLocation->state) }}"
                        maxlength="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 uppercase @error('state') border-red-400 @enderror">
                    @error('state') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ZIP</label>
                    <input type="text" name="zip" value="{{ old('zip', $pickupLocation->zip) }}"
                        maxlength="10"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('zip') border-red-400 @enderror">
                    @error('zip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" id="active" value="1"
                    {{ old('active', $pickupLocation->active) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <label for="active" class="text-sm text-gray-700">Active (visible to customers)</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                    Save Changes
                </button>
                <a href="{{ route('admin.pickup-locations.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 px-5 py-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
