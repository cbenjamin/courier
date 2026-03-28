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

    public function createCheckoutSession(string $customerId, string $priceId, string $successUrl, string $cancelUrl): \Stripe\Checkout\Session
    {
        return $this->client->checkout->sessions->create([
            'customer'   => $customerId,
            'mode'       => 'subscription',
            'line_items' => [['price' => $priceId, 'quantity' => 1]],
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $cancelUrl,
        ]);
    }

    public function getCheckoutSession(string $sessionId): \Stripe\Checkout\Session
    {
        return $this->client->checkout->sessions->retrieve($sessionId, [
            'expand' => ['subscription'],
        ]);
    }

    public function getSubscription(string $stripeSubscriptionId): Subscription
    {
        return $this->client->subscriptions->retrieve($stripeSubscriptionId);
    }

    public function cancelSubscription(string $stripeSubscriptionId, bool $immediately = false): Subscription
    {
        if ($immediately) {
            return $this->client->subscriptions->cancel($stripeSubscriptionId);
        }

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
