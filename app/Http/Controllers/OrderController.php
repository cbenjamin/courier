<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private StripeService $stripe) {}

    public function index(): View
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        $user = auth()->user()->load('profile', 'subscription');

        return view('orders.create', compact('user'));
    }

    public function store(StoreOrderRequest $request): mixed
    {
        $user = auth()->user()->load('subscription');
        $data = $request->validated();

        if ($data['type'] === Order::TYPE_SUBSCRIPTION) {
            $subscription = $user->subscription;
            abort_if(! $subscription?->isActive(), 403, 'No active subscription.');
            abort_if(! $subscription->hasCreditsRemaining(), 422, 'Monthly order limit reached. You have used all 4 orders this period.');

            $order = $user->orders()->create([
                ...$data,
                'subscription_id' => $subscription->id,
                'status'          => Order::STATUS_CONFIRMED,
            ]);

            $subscription->increment('orders_used');

            return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
        }

        // Ad-hoc: create order then get Stripe client_secret
        $order = $user->orders()->create([
            ...$data,
            'status' => Order::STATUS_PENDING,
            'amount_cents' => config('courier.adhoc_price_cents', 2500),
        ]);

        if (! $user->stripe_customer_id) {
            $customerId = $this->stripe->createCustomer($user);
            $user->update(['stripe_customer_id' => $customerId]);
        }

        $intent = $this->stripe->createPaymentIntent($order->amount_cents, $user->stripe_customer_id);

        $order->update(['stripe_payment_intent_id' => $intent->id]);

        return view('orders.payment', [
            'order' => $order,
            'clientSecret' => $intent->client_secret,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    public function show(Order $order): View
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('payment');

        return view('orders.show', compact('order'));
    }
}
