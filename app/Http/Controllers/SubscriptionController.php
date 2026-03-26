<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Notifications\SubscriptionActivatedNotification;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private StripeService $stripe) {}

    public function show(): View
    {
        $user = auth()->user()->load('subscription');

        return view('subscription.index', compact('user'));
    }

    public function store(): RedirectResponse
    {
        $user = auth()->user()->load('subscription');

        if ($user->subscription?->isActive()) {
            return redirect()->route('subscribe.show')->with('error', 'You already have an active subscription.');
        }

        if (! $user->stripe_customer_id) {
            $customerId = $this->stripe->createCustomer($user);
            $user->update(['stripe_customer_id' => $customerId]);
            $user->refresh();
        }

        $stripeSub = $this->stripe->createSubscription(
            $user->stripe_customer_id,
            config('services.stripe.price_id')
        );

        Subscription::create([
            'user_id' => $user->id,
            'stripe_subscription_id' => $stripeSub->id,
            'status' => $stripeSub->status === 'active' ? Subscription::STATUS_ACTIVE : 'pending',
            'orders_used' => 0,
            'period_start' => $stripeSub->current_period_start
                ? \Carbon\Carbon::createFromTimestamp($stripeSub->current_period_start)
                : now(),
            'period_end' => $stripeSub->current_period_end
                ? \Carbon\Carbon::createFromTimestamp($stripeSub->current_period_end)
                : now()->addMonth(),
        ]);

        $user->notify(new SubscriptionActivatedNotification());

        return redirect()->route('dashboard')->with('success', 'Subscription activated! You can now place up to 4 orders per month.');
    }
}
