<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(private StripeService $stripe) {}

    public function index(): View
    {
        $user = auth()->user()->load('subscription');

        return view('billing.index', compact('user'));
    }

    public function cancel(): RedirectResponse
    {
        $user = auth()->user()->load('subscription');
        $subscription = $user->subscription;

        abort_if(! $subscription?->isActive(), 403, 'No active subscription to cancel.');

        $this->stripe->cancelSubscription($subscription->stripe_subscription_id);
        $subscription->update(['status' => Subscription::STATUS_CANCELLING]);

        return redirect()->route('billing.index')
            ->with('success', 'Your subscription will be cancelled at the end of the current billing period.');
    }
}
