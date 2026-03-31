<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Subscription;
use App\Notifications\SubscriptionActivatedNotification;
use App\Notifications\SubscriptionCancelledNotification;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private StripeService $stripe) {}

    public function show(): View
    {
        $user = auth()->user()->load('subscription');
        $subscriptionPrice = Setting::get('subscription_price_cents', 7900) / 100;

        return view('subscription.index', compact('user', 'subscriptionPrice'));
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

        $session = $this->stripe->createCheckoutSession(
            $user->stripe_customer_id,
            config('services.stripe.price_id'),
            route('subscribe.complete'),
            route('subscribe.show'),
        );

        return redirect($session->url);
    }

    public function resume(): RedirectResponse
    {
        $user         = auth()->user()->load('subscription');
        $subscription = $user->subscription;

        abort_if($subscription?->status !== Subscription::STATUS_CANCELLING, 403, 'No cancellation to undo.');

        $this->stripe->resumeSubscription($subscription->stripe_subscription_id);

        $subscription->update(['status' => Subscription::STATUS_ACTIVE]);

        return redirect()->route('subscribe.show')
            ->with('success', 'Your subscription has been reactivated and will renew on ' . $subscription->period_end->format('M j, Y') . '.');
    }

    public function cancel(): RedirectResponse
    {
        $user = auth()->user()->load('subscription');
        $subscription = $user->subscription;

        abort_if(! $subscription?->isActive(), 403, 'No active subscription to cancel.');

        $this->stripe->cancelSubscription($subscription->stripe_subscription_id);

        $subscription->update(['status' => Subscription::STATUS_CANCELLING]);

        $user->notify(new SubscriptionCancelledNotification($subscription));

        return redirect()->route('subscribe.show')
            ->with('success', 'Your subscription has been cancelled and will remain active until ' . $subscription->period_end->format('M j, Y') . '.');
    }

    public function complete(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('subscribe.show')
                ->with('error', 'Payment could not be confirmed. Please try again.');
        }

        $user           = auth()->user()->load('subscription');
        $checkoutSession = $this->stripe->getCheckoutSession($sessionId);
        $stripeSub       = $checkoutSession->subscription;

        if (! $stripeSub) {
            return redirect()->route('subscribe.show')
                ->with('error', 'Subscription not found. Please contact support.');
        }

        // Clean up any stale pending records
        Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->delete();

        $statusMap = [
            'active'   => Subscription::STATUS_ACTIVE,
            'past_due' => Subscription::STATUS_PAST_DUE,
            'canceled' => Subscription::STATUS_CANCELLED,
        ];

        $sub = Subscription::updateOrCreate(
            ['stripe_subscription_id' => $stripeSub->id],
            [
                'user_id'      => $user->id,
                'status'       => $statusMap[$stripeSub->status] ?? 'pending',
                'orders_used'  => 0,
                'period_start' => $stripeSub->current_period_start
                    ? Carbon::createFromTimestamp($stripeSub->current_period_start)
                    : now(),
                'period_end'   => $stripeSub->current_period_end
                    ? Carbon::createFromTimestamp($stripeSub->current_period_end)
                    : now()->addMonth(),
            ]
        );

        if ($sub->isActive()) {
            $user->notify(new SubscriptionActivatedNotification());
            return redirect()->route('dashboard')
                ->with('success', 'Subscription activated! You can now place up to 4 orders per month.');
        }

        return redirect()->route('subscribe.show')
            ->with('error', 'Payment could not be confirmed. Please try again.');
    }
}
