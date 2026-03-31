@extends('layouts.app')

@section('title', 'Request Delivery')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Deliveries</a>
        <h1 class="text-2xl font-semibold text-gray-900 mt-2">Request a Delivery</h1>
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
                        <p class="text-sm font-semibold text-brand-700 mt-2">${{ number_format($adhocPrice / 100, 2) }}</p>
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
            <h2 class="font-semibold text-gray-800 mb-1">Pickup Details</h2>
            <p class="text-xs text-gray-500 mb-4">Select the store location, place your order with them, then paste the confirmation link below.</p>
            <div class="space-y-4">

                {{-- Pickup Location Typeahead --}}
                <div x-data="{
                    query: '{{ old('pickup_location_name', '') }}',
                    results: [],
                    selectedId: '{{ old('pickup_location_id', '') }}',
                    open: false,
                    loading: false,
                    async search() {
                        if (this.query.length < 1) { this.results = []; this.open = false; return; }
                        this.loading = true;
                        const res = await fetch('{{ route('pickup-locations.search') }}?q=' + encodeURIComponent(this.query));
                        this.results = await res.json();
                        this.open = this.results.length > 0;
                        this.loading = false;
                    },
                    select(loc) {
                        this.selectedId = loc.id;
                        this.query = loc.name + ' — ' + loc.city + ', ' + loc.state;
                        this.open = false;
                        this.results = [];
                    },
                    clear() {
                        this.selectedId = '';
                        this.query = '';
                        this.results = [];
                        this.open = false;
                    }
                }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Location</label>
                    <div class="relative">
                        <input type="text"
                            x-model="query"
                            @input.debounce.250ms="search()"
                            @focus="if (query.length > 0 && results.length > 0) open = true"
                            @click.outside="open = false"
                            @keydown.escape="open = false"
                            placeholder="Type a store name or city…"
                            autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('pickup_location_id') border-red-400 @enderror">

                        <input type="hidden" name="pickup_location_id" :value="selectedId">

                        {{-- Clear button --}}
                        <button type="button" x-show="selectedId || query" @click="clear()"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" x-cloak
                            class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto">
                            <template x-for="loc in results" :key="loc.id">
                                <button type="button" @click="select(loc)"
                                    class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition-colors">
                                    <p class="text-sm font-medium text-gray-900" x-text="loc.name"></p>
                                    <p class="text-xs text-gray-500 mt-0.5"
                                        x-text="loc.address + ', ' + loc.city + ', ' + loc.state + ' ' + loc.zip"></p>
                                </button>
                            </template>
                        </div>
                    </div>
                    @error('pickup_location_id')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @else
                        <p class="text-xs text-gray-400 mt-1">Search by store name, city, or both — e.g. "Whole Foods Destin"</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Pickup Link</label>
                    <input type="url" name="pickup_link"
                        value="{{ old('pickup_link') }}"
                        placeholder="https://www.amazon.com/gp/buy/thankyou/..."
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('pickup_link') border-red-400 @enderror">
                    @error('pickup_link') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">The confirmation or order-tracking link from your grocery store order.</p>
                </div>
                <div x-data="{
                    blackouts: {{ $blackoutDates->toJson() }},
                    blackoutWarning: false,
                    checkBlackout(val) {
                        if (!val) { this.blackoutWarning = false; return; }
                        const date = val.split('T')[0];
                        this.blackoutWarning = this.blackouts.includes(date);
                    }
                }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Time</label>
                    <input type="datetime-local" name="pickup_time"
                        value="{{ old('pickup_time') }}"
                        min="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}"
                        required
                        @change="checkBlackout($el.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('pickup_time') border-red-400 @enderror">
                    @error('pickup_time') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    <p x-show="blackoutWarning" x-cloak class="text-xs text-red-500 mt-1 font-medium">We are not available on this date. Please choose another.</p>
                    <p class="text-xs text-gray-400 mt-1">When should we pick up your order from the store?</p>
                </div>
            </div>
        </div>

        <!-- Delivery Address -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
            x-data="{
                serviceZips: {{ $serviceZips->toJson() }},
                zip: '{{ old('delivery_zip', $user->profile?->zip) }}',
                get zipOutOfArea() {
                    return this.serviceZips.length > 0 && this.zip.length >= 5 && !this.serviceZips.includes(this.zip.trim());
                }
            }">
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
                            x-model="zip"
                            required maxlength="10"
                            :class="zipOutOfArea ? 'border-red-400' : ''"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('delivery_zip') border-red-400 @enderror">
                        <p x-show="zipOutOfArea" x-cloak class="text-xs text-red-500 mt-1 font-medium">
                            We don't currently deliver to this ZIP code.
                            <a href="{{ route('contact.show') }}" class="underline">Contact us</a> to request coverage.
                        </p>
                        @error('delivery_zip') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
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
