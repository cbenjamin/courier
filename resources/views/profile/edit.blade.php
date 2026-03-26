@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">My Profile</h1>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <div class="mb-6 pb-6 border-b border-gray-100">
            <p class="text-sm text-gray-500">Account</p>
            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone"
                    value="{{ old('phone', $user->profile?->phone) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                <input type="text" name="address"
                    value="{{ old('address', $user->profile?->address) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city"
                        value="{{ old('city', $user->profile?->city) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input type="text" name="state" maxlength="2"
                        value="{{ old('state', $user->profile?->state ?? 'AL') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ZIP</label>
                    <input type="text" name="zip"
                        value="{{ old('zip', $user->profile?->zip) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Delivery Notes <span class="text-gray-400 font-normal">(gate codes, directions, etc.)</span>
                </label>
                <textarea name="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('notes', $user->profile?->notes) }}</textarea>
            </div>

            <button type="submit"
                class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">
                Save Profile
            </button>
        </form>
    </div>
</div>
@endsection
