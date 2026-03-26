@extends('layouts.app')

@section('title', 'Billing')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Billing</h1>

    @if($user->subscription)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="font-semibold text-gray-900">Monthly Subscription</p>
                    <p class="text-sm text-gray-500 mt-0.5">$69.00 / month</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                    @if($user->subscription->status === 'active') bg-green-100 text-green-700
                    @elseif($user->subscription->status === 'cancelling') bg-orange-100 text-orange-700
                    @elseif($user->subscription->status === 'past_due') bg-red-100 text-red-700
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ ucfirst(str_replace('_', ' ', $user->subscription->status)) }}
                </span>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Current period</dt>
                    <dd class="text-gray-800 font-medium">
                        {{ $user->subscription->period_start?->format('M j') }} –
                        {{ $user->subscription->period_end?->format('M j, Y') }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Orders used</dt>
                    <dd class="text-gray-800 font-medium">{{ $user->subscription->orders_used }} / 4</dd>
                </div>
            </dl>

            @if($user->subscription->isActive() && $user->subscription->status !== 'cancelling')
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <form method="POST" action="{{ route('billing.cancel') }}"
                        x-data
                        @submit.prevent="confirm('Are you sure you want to cancel? Your subscription will remain active until the end of the current billing period.') && $el.submit()">
                        @csrf
                        <button type="submit"
                            class="text-sm text-red-600 hover:text-red-700 font-medium">
                            Cancel Subscription
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 mt-1">You'll keep access until {{ $user->subscription->period_end?->format('M j, Y') }}</p>
                </div>
            @elseif($user->subscription->status === 'cancelling')
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <p class="text-sm text-orange-600 font-medium">Cancellation scheduled</p>
                    <p class="text-xs text-gray-400 mt-1">Access ends {{ $user->subscription->period_end?->format('M j, Y') }}</p>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
            <p class="text-gray-500 mb-4">You don't have an active subscription.</p>
            <a href="{{ route('subscribe.show') }}"
                class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                View Plans
            </a>
        </div>
    @endif
</div>
@endsection
