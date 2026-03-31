@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="max-w-2xl space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Pricing -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-1">Pricing</h2>
        <p class="text-sm text-gray-500 mb-5">Controls the prices shown to customers and charged via Stripe.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <input type="hidden" name="contact_email" value="{{ $settings['contact_email'] }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ad-hoc delivery price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                        <input type="number" name="adhoc_price" step="0.01" min="1"
                            value="{{ number_format($settings['adhoc_price_cents'] / 100, 2, '.', '') }}"
                            class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('adhoc_price') border-red-400 @enderror"
                            placeholder="25.00">
                    </div>
                    @error('adhoc_price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subscription price (per month)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                        <input type="number" name="subscription_price" step="0.01" min="1"
                            value="{{ number_format($settings['subscription_price_cents'] / 100, 2, '.', '') }}"
                            class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('subscription_price') border-red-400 @enderror"
                            placeholder="79.00">
                    </div>
                    @error('subscription_price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">Note: the actual Stripe charge is controlled by your Stripe price ID and must be updated there separately.</p>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-5 py-2 rounded-lg text-sm transition-colors">
                    Save Pricing
                </button>
            </div>
        </form>
    </div>

    <!-- Contact Form Settings -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-1">Contact Form</h2>
        <p class="text-sm text-gray-500 mb-5">Contact form submissions will be emailed to this address.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <input type="hidden" name="adhoc_price" value="{{ number_format($settings['adhoc_price_cents'] / 100, 2, '.', '') }}">
            <input type="hidden" name="subscription_price" value="{{ number_format($settings['subscription_price_cents'] / 100, 2, '.', '') }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recipient email</label>
                <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('contact_email') border-red-400 @enderror"
                    placeholder="you@example.com">
                @error('contact_email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-5 py-2 rounded-lg text-sm transition-colors">
                Save Contact Settings
            </button>
        </form>
    </div>

    <!-- Service Area -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Service Area (ZIP Codes)</h2>
            <p class="text-sm text-gray-500 mt-1">
                Customers can only place delivery orders to these ZIP codes.
                @if($serviceZips->isEmpty())
                    <span class="text-amber-600 font-medium">No ZIPs configured — deliveries are currently open to any address.</span>
                @endif
            </p>
        </div>

        @forelse($serviceZips as $serviceZip)
            <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-800 font-mono">{{ $serviceZip->zip }}</p>
                    @if($serviceZip->label)
                        <p class="text-xs text-gray-400">{{ $serviceZip->label }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.service-zips.destroy', $serviceZip) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors">Remove</button>
                </form>
            </div>
        @empty
            <p class="px-6 py-4 text-sm text-gray-400">No ZIP codes added yet.</p>
        @endforelse

        <div class="px-6 py-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            <p class="text-sm font-medium text-gray-700 mb-3">Add a ZIP code</p>
            <form method="POST" action="{{ route('admin.settings.service-zips.store') }}" class="flex gap-3 flex-wrap">
                @csrf
                <div>
                    <input type="text" name="zip" maxlength="10" placeholder="36301" required
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500 w-28 @error('zip') border-red-400 @enderror">
                    @error('zip')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1 min-w-48">
                    <input type="text" name="label" placeholder="Label, e.g. Dothan, AL (optional)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors whitespace-nowrap">
                    Add ZIP
                </button>
            </form>
        </div>
    </div>

    <!-- Blackout Dates -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Delivery Blackout Dates</h2>
            <p class="text-sm text-gray-500 mt-1">Customers cannot schedule a delivery on these dates.</p>
        </div>

        <!-- Existing blackouts -->
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

        <!-- Add new blackout -->
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
