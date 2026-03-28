<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private StripeService $stripe) {}

    public function index(): View
    {
        $subscriptions = Subscription::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['user', 'orders' => fn ($q) => $q->latest()]);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function cancel(Subscription $subscription): RedirectResponse
    {
        abort_if(! $subscription->isActive(), 422, 'Subscription is not active.');

        $this->stripe->cancelSubscription($subscription->stripe_subscription_id, immediately: true);

        $subscription->update(['status' => Subscription::STATUS_CANCELLED]);

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription for ' . $subscription->user->name . ' has been cancelled immediately.');
    }
}
