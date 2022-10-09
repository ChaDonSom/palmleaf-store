<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Lunar\Models\Cart;
use Lunar\Stripe\Facades\StripeFacade;
use Stripe\Stripe;

class PaypalPaymentForm extends Component
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
        $this->policy = config('paypal.policy', 'manual');
    }

    /**
     * Return the client secret for Payment Intent
     *
     * @return void
     */
    public function getClientSecretProperty()
    {
        // $intent = StripeFacade::createIntent($this->cart);
        // return $intent->client_secret;
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

    public function render()
    {
        return view('livewire.paypal-payment-form');
    }
}
