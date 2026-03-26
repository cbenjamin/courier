<?php

namespace App\Services;

use App\Models\User;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Stripe\Subscription;

class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    public function createCustomer(User $user): string
    {
        $customer = $this->client->customers->create([
            'name' => $user->name,
            'email' => $user->email,
        ]);

        return $customer->id;
    }

    public function createPaymentIntent(int $amountCents, string $customerId): PaymentIntent
    {
        return $this->client->paymentIntents->create([
            'amount' => $amountCents,
            'currency' => 'usd',
            'customer' => $customerId,
            'automatic_payment_methods' => ['enabled' => true],
        ]);
    }

    public function createSubscription(string $customerId, string $priceId): Subscription
    {
        return $this->client->subscriptions->create([
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
            'expand' => ['latest_invoice.payment_intent'],
        ]);
    }

    public function cancelSubscription(string $stripeSubscriptionId): Subscription
    {
        return $this->client->subscriptions->update($stripeSubscriptionId, [
            'cancel_at_period_end' => true,
        ]);
    }

    public function constructWebhookEvent(string $payload, string $signature): \Stripe\Event
    {
        return \Stripe\Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );
    }
}
