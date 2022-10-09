<?php

namespace App\Managers;

use Exception;
use GetCandy\Models\Cart;
use Srmklive\PayPal\Services\PayPal;

class PaypalManager
{
    public function __construct(
        private ?PayPal $paypal = null
    )
    {
        $this->paypal = $this->getClient();
    }

    /**
     * Return the PayPal client
     *
     * @return PayPal
     */
    public function getClient(): PayPal
    {
        $paypal = new PayPal;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);
        return $paypal;
    }

    /**
     * Create an order from a Cart
     *
     * @param Cart $cart
     * @return object (order from paypal API)
     */
    public function createOrder(Cart $cart): object
    {
        $cart = $cart->getManager()->getCart();
        
        $shipping = $cart->shippingAddress;

        $meta = $cart->meta;

        if ($meta && isset($meta->paypal_order_id)) {
            $order = $this->fetchOrder($meta->paypal_order_id);

            if ($order) return (object) $order;
        }

        $paypalOrder = $this->buildOrder(
            $cart->total->value,
            $cart->currency->code,
            $shipping,
        );

        if (!$meta) {
            $cart->update([
                'meta' => [
                    'paypal_order_id' => $paypalOrder->id,
                ],
            ]);
        } else {
            $meta->paypal_order_id = $paypalOrder->id;
            $cart->meta = $meta;
            $cart->save();
        }

        return $paypalOrder;
    }

    /**
     * Fetch an order from the Paypal API.
     *
     * @param string $intentId
     * @return null|\Stripe\PaymentIntent
     */
    public function fetchOrder($orderId)
    {
        try {
            $order = $this->paypal->showOrderDetails($orderId);
        } catch (Exception $e) {
            return null;
        }

        return $order;
    }

    /**
     * Build the intent
     *
     * @param int $value
     * @param string $currencyCode
     * @param \GetCandy\Models\CartAddress $shipping
     * @return object
     */
    protected function buildOrder($value, $currencyCode, $shipping)
    {
        return (object) $this->paypal->createOrder([
            "intent" => config('getcandy.paypal.policy', 'automatic') == 'automatic' ? 'CAPTURE' : 'AUTHORIZE',
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $currencyCode,
                        "value" => $value / 100
                    ],
                ]
            ],
            'shipping' => [
                'name' => "{$shipping->first_name} {$shipping->last_name}",
                'address' => [
                    'city' => $shipping->city,
                    'country' => $shipping->country->iso2,
                    'line1' => $shipping->line_one,
                    'line2' => $shipping->line_two,
                    'postal_code' => $shipping->postcode,
                    'state' => $shipping->state,
                ],
            ],
        ]);
    }
}
