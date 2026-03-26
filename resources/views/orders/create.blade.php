@extends('layouts.app')

@section('title', 'New Order')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Orders</a>
        <h1 class="text-2xl font-semibold text-gray-900 mt-2">New Order</h1>
        <p class="text-gray-500 text-sm mt-1">We'll pick up your items from Whole Foods and deliver them to you.</p>
    </div>

    <form method="POST" action="{{ route('orders.store') }}" class="space-y-6"
        x-data="{
            orderType: '{{ $user->subscription?->isActive() && $user->subscription->hasCreditsRemaining() ? 'subscription' : 'adhoc' }}',
            items: [{ name: '', quantity: 1 }],
            addItem() { this.items.push({ name: '', quantity: 1 }) },
            removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) }
        }">
        @csrf

        <!-- Order Type -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
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

        <!-- Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">Items from Whole Foods</h2>
                <button type="button" @click="addItem()"
                    class="text-sm text-brand-600 hover:text-brand-700 font-medium">
                    + Add Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex gap-3 items-start">
                        <div class="flex-1">
                            <input type="text" :name="'items[' + index + '][name]'" x-model="item.name"
                                placeholder="Item name (e.g. Organic Apples)"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="w-20">
                            <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity"
                                min="1" max="99"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <button type="button" @click="removeItem(index)"
                            x-show="items.length > 1"
                            class="mt-2 text-gray-300 hover:text-red-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            @error('items')
                <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Delivery Details -->
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

        <!-- Schedule & Notes -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Schedule</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Delivery Date & Time</label>
                    <input type="datetime-local" name="scheduled_at"
                        value="{{ old('scheduled_at') }}"
                        min="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('scheduled_at') border-red-400 @enderror">
                    @error('scheduled_at') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="notes" rows="3" placeholder="Any substitutions, access codes, or other notes..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl transition-colors">
            <span x-text="orderType === 'adhoc' ? 'Continue to Payment' : 'Place Order'"></span>
        </button>
    </form>
</div>
@endsection
