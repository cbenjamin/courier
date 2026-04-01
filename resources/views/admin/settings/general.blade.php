@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="max-w-2xl">

    @include('admin.settings._subnav')

    <div class="space-y-6">

        <!-- Pricing -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-1">Pricing</h2>
            <p class="text-sm text-gray-500 mb-5">Controls the prices shown to customers and charged via Stripe.</p>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

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

                {{-- Pass pricing through so it isn't overwritten --}}
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

    </div>
</div>
@endsection
