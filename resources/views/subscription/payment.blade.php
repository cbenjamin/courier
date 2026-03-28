@extends('layouts.app')

@section('title', 'Complete Subscription')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Complete Your Subscription</h1>
        <p class="text-gray-500 text-sm mt-1">Wiregrass Courier Monthly Plan &mdash; $79.00/month</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
        <h2 class="font-semibold text-gray-800 mb-3">What's included</h2>
        <ul class="space-y-2">
            @foreach(['Up to 4 Whole Foods courier runs per month', 'Save vs. $25/order ad-hoc rate', 'Priority scheduling', 'Cancel anytime'] as $feature)
                <li class="flex items-center gap-3 text-sm text-gray-700">
                    <svg class="w-4 h-4 text-brand-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $feature }}
                </li>
            @endforeach
        </ul>
        <div class="border-t border-gray-100 pt-3 mt-4 flex justify-between text-sm font-semibold text-gray-800">
            <span>Monthly total</span>
            <span>{{ $amount }}</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
        x-data="stripePayment('{{ $clientSecret }}', '{{ $stripeKey }}')">
        <div id="payment-element" class="mb-4"></div>
        <div id="payment-message" class="text-red-600 text-sm mb-3 hidden"></div>
        <button id="submit-btn" @click="submit()"
            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl transition-colors"
            :disabled="loading">
            <span x-text="loading ? 'Processing...' : 'Subscribe for ' + amount"></span>
        </button>
        <p class="text-xs text-gray-400 text-center mt-3">Cancel anytime. No contracts.</p>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
function stripePayment(clientSecret, stripeKey) {
    return {
        loading: false,
        amount: '{{ $amount }}/mo',
        stripe: null,
        elements: null,

        init() {
            this.stripe = Stripe(stripeKey);
            this.elements = this.stripe.elements({ clientSecret });
            const paymentElement = this.elements.create('payment');
            paymentElement.mount('#payment-element');
        },

        async submit() {
            this.loading = true;
            document.getElementById('payment-message').classList.add('hidden');

            const { error } = await this.stripe.confirmPayment({
                elements: this.elements,
                confirmParams: {
                    return_url: '{{ route('subscribe.complete', $subscription) }}',
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
@endsection
