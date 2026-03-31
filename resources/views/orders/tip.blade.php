@extends('layouts.app')

@section('title', 'Leave a Tip — Delivery #' . $order->id)

@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-6">
        <a href="{{ route('orders.show', $order) }}" class="text-sm text-gray-400 hover:text-gray-600">← Delivery #{{ $order->id }}</a>
        <h1 class="text-2xl font-semibold text-gray-900 mt-2">Leave a Tip</h1>
        <p class="text-gray-500 text-sm mt-1">100% goes directly to your courier. Completely optional.</p>
    </div>

    @if($clientSecret)
        {{-- Step 2: Payment form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
            <p class="text-sm font-medium text-gray-700 mb-1">Tip amount</p>
            <p class="text-2xl font-bold text-brand-700">{{ $tipAmountFormatted }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
            x-data="tipPayment('{{ $clientSecret }}', '{{ config('services.stripe.key') }}')">
            <div id="payment-element" class="mb-4"></div>
            <div id="payment-message" class="text-red-600 text-sm mb-3 hidden"></div>
            <button @click="submit()" :disabled="loading"
                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl transition-colors">
                <span x-text="loading ? 'Processing...' : 'Send Tip'"></span>
            </button>
            <p class="text-xs text-center text-gray-400 mt-3">Secured by Stripe</p>
        </div>

        <script src="https://js.stripe.com/v3/"></script>
        <script>
        function tipPayment(clientSecret, stripeKey) {
            return {
                loading: false,
                stripe: null,
                elements: null,

                init() {
                    this.stripe = Stripe(stripeKey);
                    this.elements = this.stripe.elements({ clientSecret });
                    this.elements.create('payment').mount('#payment-element');
                },

                async submit() {
                    this.loading = true;
                    document.getElementById('payment-message').classList.add('hidden');

                    const { error } = await this.stripe.confirmPayment({
                        elements: this.elements,
                        confirmParams: {
                            return_url: '{{ route('orders.show', $order) }}',
                        },
                    });

                    if (error) {
                        document.getElementById('payment-message').textContent = error.message;
                        document.getElementById('payment-message').classList.remove('hidden');
                    }

                    this.loading = false;
                }
            }
        }
        </script>

    @else
        {{-- Step 1: Amount selection --}}
        <form method="POST" action="{{ route('orders.tip.store', $order) }}"
            x-data="{ selected: 300, custom: '' }">
            @csrf

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
                <p class="text-sm font-medium text-gray-700 mb-4">Select a tip amount</p>

                <div class="grid grid-cols-4 gap-3 mb-4">
                    @foreach([200 => '$2', 300 => '$3', 500 => '$5', 1000 => '$10'] as $cents => $label)
                        <button type="button"
                            @click="selected = {{ $cents }}; custom = ''"
                            :class="selected === {{ $cents }} && custom === '' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-gray-200 text-gray-700'"
                            class="border-2 rounded-xl py-3 text-sm font-semibold transition-colors">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Custom amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                        <input type="number" step="0.01" min="1" placeholder="0.00"
                            x-model="custom"
                            @input="if (custom) selected = 0"
                            class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <input type="hidden" name="tip_cents"
                    :value="custom ? Math.round(parseFloat(custom) * 100) : selected">
            </div>

            @error('tip_cents')
                <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl transition-colors">
                Continue to Payment
            </button>

            <p class="text-center mt-4">
                <a href="{{ route('orders.show', $order) }}" class="text-sm text-gray-400 hover:text-gray-600">
                    No thanks, skip
                </a>
            </p>
        </form>
    @endif
</div>
@endsection
