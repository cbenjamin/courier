<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Subscription;
use App\Notifications\OrderPlacedNotification;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    public function __construct(private StripeService $stripe) {}

    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = $this->stripe->constructWebhookEvent($payload, $signature);
        } catch (SignatureVerificationException) {
            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event->data->object),
            'invoice.paid' => $this->handleInvoicePaid($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            default => null,
        };

        return response('OK', 200);
    }

    private function handlePaymentIntentSucceeded(\Stripe\PaymentIntent $intent): void
    {
        // Handle tip payments first
        $tipOrder = Order::where('tip_stripe_payment_intent_id', $intent->id)->first();
        if ($tipOrder) {
            $tipOrder->update(['tip_cents' => $intent->amount]);
            return;
        }

        $order = Order::where('stripe_payment_intent_id', $intent->id)->first();

        if (! $order || $order->status === Order::STATUS_CONFIRMED) {
            return;
        }

        $order->update(['status' => Order::STATUS_CONFIRMED]);

        if (! $order->payment) {
            Payment::create([
                'order_id' => $order->id,
                'stripe_payment_intent_id' => $intent->id,
                'amount_cents' => $intent->amount,
                'status' => 'succeeded',
                'paid_at' => now(),
            ]);
        }

        $order->user->notify(new OrderPlacedNotification($order));
    }

    private function handleInvoicePaid(\Stripe\Invoice $invoice): void
    {
        if (! $invoice->subscription) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();

        if (! $subscription) {
            return;
        }

        $subscription->update([
            'orders_used' => 0,
            'period_start' => $invoice->period_start ? Carbon::createFromTimestamp($invoice->period_start) : now(),
            'period_end' => $invoice->period_end ? Carbon::createFromTimestamp($invoice->period_end) : now()->addMonth(),
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    }

    private function handleSubscriptionDeleted(\Stripe\Subscription $stripeSub): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSub->id)->first();
        $subscription?->update(['status' => Subscription::STATUS_CANCELLED]);
    }

    private function handleSubscriptionUpdated(\Stripe\Subscription $stripeSub): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSub->id)->first();

        if (! $subscription) {
            return;
        }

        $statusMap = [
            'active' => $stripeSub->cancel_at_period_end ? Subscription::STATUS_CANCELLING : Subscription::STATUS_ACTIVE,
            'past_due' => Subscription::STATUS_PAST_DUE,
            'canceled' => Subscription::STATUS_CANCELLED,
            'incomplete' => 'pending',
        ];

        $newStatus = $statusMap[$stripeSub->status] ?? $subscription->status;
        $subscription->update(['status' => $newStatus]);
    }
}
