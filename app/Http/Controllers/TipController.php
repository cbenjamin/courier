<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipController extends Controller
{
    public function __construct(private StripeService $stripe) {}

    public function show(Order $order): View|RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->status !== Order::STATUS_DELIVERED || $order->tip_cents !== null) {
            return redirect()->route('orders.show', $order);
        }

        $clientSecret = null;
        $tipAmountFormatted = null;

        if ($order->tip_stripe_payment_intent_id) {
            $intent = $this->stripe->getPaymentIntent($order->tip_stripe_payment_intent_id);
            $clientSecret = $intent->client_secret;
            $tipAmountFormatted = '$'.number_format($intent->amount / 100, 2);
        }

        return view('orders.tip', compact('order', 'clientSecret', 'tipAmountFormatted'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->status === Order::STATUS_DELIVERED && $order->tip_cents === null, 403);

        $validated = $request->validate([
            'tip_cents' => ['required', 'integer', 'min:100'],
        ]);

        // Idempotent: only create if not already pending
        if (! $order->tip_stripe_payment_intent_id) {
            $user = auth()->user();

            if (! $user->stripe_customer_id) {
                $customerId = $this->stripe->createCustomer($user);
                $user->update(['stripe_customer_id' => $customerId]);
            }

            $intent = $this->stripe->createTipPaymentIntent(
                $validated['tip_cents'],
                $user->stripe_customer_id,
                $order->id
            );

            $order->update(['tip_stripe_payment_intent_id' => $intent->id]);
        }

        return redirect()->route('orders.tip', $order);
    }
}
