@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Complete Your Delivery Request</h1>
        <p class="text-gray-500 text-sm mt-1">Delivery #{{ $order->id }} &mdash; {{ $order->amount_formatted }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
        <h2 class="font-semibold text-gray-800 mb-3">Order Summary</h2>
        <dl class="space-y-2 mb-4">
            <div class="text-sm">
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Pickup Link</dt>
                <dd class="text-gray-700 break-all">{{ $order->pickup_link }}</dd>
            </div>
            @if($order->pickup_time)
                <div class="text-sm">
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Pickup Time</dt>
                    <dd class="text-gray-700">{{ $order->pickup_time->format('M j, Y g:i A') }}</dd>
                </div>
            @endif
            <div class="text-sm">
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Deliver To</dt>
                <dd class="text-gray-700">{{ $order->delivery_address }}, {{ $order->delivery_city }}, {{ $order->delivery_state }} {{ $order->delivery_zip }}</dd>
            </div>
        </dl>
        <div class="border-t border-gray-100 pt-3 flex justify-between text-sm font-semibold text-gray-800">
            <span>Courier Fee</span>
            <span>{{ $order->amount_formatted }}</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
        x-data="stripePayment('{{ $clientSecret }}', '{{ $stripeKey }}')">
        <div id="payment-element" class="mb-4"></div>
        <div id="payment-message" class="text-red-600 text-sm mb-3 hidden"></div>
        <button id="submit-btn" @click="submit()"
            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl transition-colors"
            :disabled="loading">
            <span x-text="loading ? 'Processing...' : 'Pay ' + amount"></span>
        </button>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
function stripePayment(clientSecret, stripeKey) {
    return {
        loading: false,
        amount: '{{ $order->amount_formatted }}',
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
@endsection
