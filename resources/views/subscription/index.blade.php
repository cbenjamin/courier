@extends('layouts.app')

@section('title', 'Subscription')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Monthly Subscription</h1>

    @if($user->subscription?->isActive())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-brand-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Active Subscription</p>
                    @if($user->subscription->status === 'cancelling')
                        <p class="text-sm text-orange-500">Cancels {{ $user->subscription->period_end?->format('M j, Y') }}</p>
                    @else
                        <p class="text-sm text-gray-500">Renews {{ $user->subscription->period_end?->format('M j, Y') }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium text-gray-700">Deliveries used this month</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $user->subscription->orders_used }} / 4</p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-brand-600 h-2 rounded-full transition-all"
                        style="width: {{ ($user->subscription->orders_used / 4) * 100 }}%"></div>
                </div>
            </div>

            <div class="flex gap-3 mb-4">
                <a href="{{ route('orders.create') }}"
                    class="flex-1 text-center bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
                    Request Delivery
                </a>
                <a href="{{ route('billing.index') }}"
                    class="flex-1 text-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
                    Manage Billing
                </a>
            </div>

            @if($user->subscription->status !== 'cancelling')
                <div x-data="{ open: false }" class="border-t border-gray-100 pt-4">
                    <button @click="open = true"
                        class="text-sm text-red-500 hover:text-red-700 transition-colors">
                        Cancel subscription
                    </button>

                    <!-- Confirmation modal -->
                    <div x-show="open" x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                        @keydown.escape.window="open = false">
                        <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full mx-4" @click.stop>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Cancel subscription?</h3>
                            <p class="text-sm text-gray-500 mb-6">
                                You'll keep access to your remaining orders until
                                <strong>{{ $user->subscription->period_end?->format('M j, Y') }}</strong>.
                                After that your subscription will not renew.
                            </p>
                            <div class="flex gap-3">
                                <button @click="open = false"
                                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 rounded-lg text-sm transition-colors">
                                    Keep subscription
                                </button>
                                <form method="POST" action="{{ route('subscribe.cancel') }}" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg text-sm transition-colors">
                                        Yes, cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm text-orange-500">
                        Your subscription is cancelled and will remain active until {{ $user->subscription->period_end?->format('M j, Y') }}.
                    </p>
                </div>
            @endif
        </div>
    @else
        <!-- Upsell -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-brand-700 px-8 py-6 text-white">
                <p class="text-brand-300 text-sm font-medium uppercase tracking-wide mb-1">Monthly Plan</p>
                <p class="text-4xl font-bold">${{ number_format($subscriptionPrice, 0) }}<span class="text-xl font-normal text-brand-300">/mo</span></p>
                <p class="text-brand-200 mt-1">Up to 4 deliveries per month</p>
            </div>
            <div class="p-8">
                <ul class="space-y-3 mb-8">
                    @foreach(['Up to 4 Whole Foods courier deliveries per month', 'Save vs. $25/delivery ad-hoc rate', 'Priority scheduling', 'Cancel anytime'] as $feature)
                        <li class="flex items-center gap-3 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>

                <form method="POST" action="{{ route('subscribe.store') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl transition-colors">
                        Subscribe Now
                    </button>
                </form>

                <p class="text-xs text-gray-400 text-center mt-3">Cancel anytime. No contracts.</p>
            </div>
        </div>
    @endif
</div>
@endsection
