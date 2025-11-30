<?php

namespace App\Livewire;

use Lunar\Models\Cart;
use Livewire\Component;
use Lunar\Stripe\Facades\Stripe as FacadesStripe;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentForm extends Component
{
    /**
     * The instance of the order.
     *
     * @var Order
     */
    public Cart $cart;

    /**
     * The return URL on a successful transaction
     *
     * @var string
     */
    public $returnUrl;

    /**
     * The policy for handling payments.
     *
     * @var string
     */
    public $policy;

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'cardDetailsSubmitted',
    ];

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        Stripe::setApiKey(config('services.stripe.key'));
        $this->policy = config('stripe.policy', 'capture');
    }

    /**
     * Return the client secret for Payment Intent
     *
     * @return void
     */
    public function getClientSecretProperty()
    {
        // Ensure cart is calculated to have correct totals before creating/syncing payment intent
        // This is critical to prevent charging incorrect amounts when cart contents change
        $this->cart->calculate();

        // Don't cancel payment intents if we're processing a return from Stripe
        // (indicated by payment_intent in query params)
        if (!request()->has('payment_intent')) {
            // Cancel any existing payment intent that requires capture
            // to prevent "payment_intent_unexpected_state" errors
            $existingIntent = $this->cart->paymentIntents()->active()->first();
            if ($existingIntent && $existingIntent->status === 'requires_capture') {
                FacadesStripe::cancelIntent($this->cart, \Lunar\Stripe\Enums\CancellationReason::ABANDONED);
            }
        }

        // Sync the payment intent amount with the current cart total
        // This ensures any changes to cart contents are reflected in the payment
        FacadesStripe::syncIntent($this->cart);

        $intent = FacadesStripe::createIntent($this->cart);
        return $intent->client_secret;
    }

    /**
     * Return the carts billing address.
     *
     * @return void
     */
    public function getBillingProperty()
    {
        return $this->cart->billingAddress;
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        return view('livewire.payment-form');
    }
}
