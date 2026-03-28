@extends('layouts.app')

@section('title', 'New Order')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Orders</a>
        <h1 class="text-2xl font-semibold text-gray-900 mt-2">New Order</h1>
        <p class="text-gray-500 text-sm mt-1">Place your Whole Foods order first, then share the pickup link with us and we'll handle the rest.</p>
    </div>

    <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
        @csrf

        <!-- Order Type -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6" x-data="{ orderType: '{{ $user->subscription?->isActive() && $user->subscription->hasCreditsRemaining() ? 'subscription' : 'adhoc' }}' }">
            <h2 class="font-semibold text-gray-800 mb-4">Payment Method</h2>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="adhoc" x-model="orderType" class="sr-only">
                    <div :class="orderType === 'adhoc' ? 'border-brand-500 bg-brand-50' : 'border-gray-200'"
                        class="border-2 rounded-xl p-4 transition-colors">
                        <p class="font-medium text-sm text-gray-900">One-time</p>
                        <p class="text-xs text-gray-500 mt-1">Pay per order via card</p>
                        <p class="text-sm font-semibold text-brand-700 mt-2">$25.00</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="type" value="subscription" x-model="orderType" class="sr-only"
                        @if(!$user->subscription?->isActive() || !$user->subscription->hasCreditsRemaining()) disabled @endif>
                    <div :class="orderType === 'subscription' ? 'border-brand-500 bg-brand-50' : 'border-gray-200'"
                        class="border-2 rounded-xl p-4 transition-colors
                        @if(!$user->subscription?->isActive() || !$user->subscription->hasCreditsRemaining()) opacity-50 cursor-not-allowed @endif">
                        <p class="font-medium text-sm text-gray-900">Subscription</p>
                        @if($user->subscription?->isActive())
                            <p class="text-xs text-gray-500 mt-1">
                                {{ 4 - $user->subscription->orders_used }} of 4 credits left
                            </p>
                            @if(!$user->subscription->hasCreditsRemaining())
                                <p class="text-xs text-red-500 mt-1">No credits remaining</p>
                            @endif
                        @else
                            <p class="text-xs text-gray-500 mt-1">No active subscription</p>
                            <a href="{{ route('subscribe.show') }}" class="text-xs text-brand-600 hover:underline mt-1 inline-block">Subscribe →</a>
                        @endif
                        <p class="text-sm font-semibold text-brand-700 mt-2">Included</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Pickup Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800 mb-1">Whole Foods Pickup</h2>
            <p class="text-xs text-gray-500 mb-4">Place your order at wholefoodsmarket.com, then paste the pickup confirmation link below.</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Pickup Link</label>
                    <input type="url" name="pickup_link"
                        value="{{ old('pickup_link') }}"
                        placeholder="https://www.amazon.com/gp/buy/thankyou/..."
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('pickup_link') border-red-400 @enderror">
                    @error('pickup_link') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">The confirmation or order-tracking link from your Whole Foods / Amazon order.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Time</label>
                    <input type="datetime-local" name="pickup_time"
                        value="{{ old('pickup_time') }}"
                        min="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('pickup_time') border-red-400 @enderror">
                    @error('pickup_time') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">When should we pick up your order from the store?</p>
                </div>
            </div>
        </div>

        <!-- Delivery Address -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Delivery Address</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                    <input type="text" name="delivery_address"
                        value="{{ old('delivery_address', $user->profile?->address) }}"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('delivery_address') border-red-400 @enderror">
                    @error('delivery_address') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" name="delivery_city"
                            value="{{ old('delivery_city', $user->profile?->city) }}"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <input type="text" name="delivery_state"
                            value="{{ old('delivery_state', $user->profile?->state ?? 'AL') }}"
                            maxlength="2" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ZIP</label>
                        <input type="text" name="delivery_zip"
                            value="{{ old('delivery_zip', $user->profile?->zip) }}"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Additional Notes <span class="text-gray-400 font-normal text-sm">(optional)</span></h2>
            <textarea name="notes" rows="3"
                placeholder="Gate codes, special delivery instructions, contact preferences..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('notes') }}</textarea>
        </div>

        <button type="submit"
            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl transition-colors"
            x-data x-text="document.querySelector('[name=type]:checked')?.value === 'subscription' ? 'Place Order' : 'Continue to Payment'">
            Continue to Payment
        </button>
    </form>
</div>
@endsection
