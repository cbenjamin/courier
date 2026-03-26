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
                    <p class="text-sm font-medium text-gray-700">Orders used this month</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $user->subscription->orders_used }} / 4</p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-brand-600 h-2 rounded-full transition-all"
                        style="width: {{ ($user->subscription->orders_used / 4) * 100 }}%"></div>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('orders.create') }}"
                    class="flex-1 text-center bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
                    Place Order
                </a>
                <a href="{{ route('billing.index') }}"
                    class="flex-1 text-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
                    Manage Billing
                </a>
            </div>
        </div>
    @else
        <!-- Upsell -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-brand-700 px-8 py-6 text-white">
                <p class="text-brand-300 text-sm font-medium uppercase tracking-wide mb-1">Monthly Plan</p>
                <p class="text-4xl font-bold">$69<span class="text-xl font-normal text-brand-300">/mo</span></p>
                <p class="text-brand-200 mt-1">Up to 4 orders per month</p>
            </div>
            <div class="p-8">
                <ul class="space-y-3 mb-8">
                    @foreach(['Up to 4 Whole Foods courier runs per month', 'Save vs. $25/order ad-hoc rate', 'Priority scheduling', 'Cancel anytime'] as $feature)
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
