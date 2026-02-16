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
     * Cached client secret to prevent multiple API calls within a single request.
     * Keyed by cart total to detect cart changes and invalidate cache.
     * 
     * Note: This cache is intentionally in-memory and scoped to the component instance.
     * It does NOT persist across page refreshes or component recreations, which is
     * correct behavior for security reasons. The cache only prevents redundant API
     * calls during a single render cycle (e.g., when the property is accessed multiple
     * times in the view).
     *
     * @var array{secret: string, total: int}|null
     */
    protected $cachedClientSecret = null;

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

        // Return cached client secret if available AND cart total hasn't changed
        // This prevents multiple API calls during Livewire re-renders while ensuring
        // cart changes invalidate the cache
        if ($this->cachedClientSecret !== null && 
            $this->cachedClientSecret['total'] === $this->cart->total->value) {
            return $this->cachedClientSecret['secret'];
        }

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

        // Sync the payment intent amount with the current cart total.
        // This is necessary because createIntent() will return an existing intent
        // without updating its amount if one already exists for this cart.
        // By calling syncIntent() first, we ensure the amount is correct
        // regardless of whether createIntent() creates a new intent or returns existing.
        FacadesStripe::syncIntent($this->cart);

        $intent = FacadesStripe::createIntent($this->cart);
        
        // Cache the client secret along with cart total to invalidate cache if cart changes
        $this->cachedClientSecret = [
            'secret' => $intent->client_secret,
            'total' => $this->cart->total->value,
        ];
        
        return $this->cachedClientSecret['secret'];
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
