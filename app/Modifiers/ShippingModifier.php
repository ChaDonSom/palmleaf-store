<?php

namespace App\Modifiers;

use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;

class ShippingModifier
{
    public function handle(Cart $cart, \Closure $next)
    {
        if (config('shipping-tables.enabled') == false) {
            // Calculate total quantity of items in cart
            $totalQuantity = $cart->lines->sum('quantity');
            
            // Calculate shipping price based on requirements:
            // - 1 item = $7.50 (750 cents)
            // - 2+ items = $12.00 (1200 cents)
            // - Free when cart total >= $50 (5000 cents)
            $shippingPrice = 0;
            
            // Check if cart total is less than $50
            if ($cart->subTotal && $cart->subTotal->value < 5000) {
                if ($totalQuantity === 1) {
                    $shippingPrice = 750; // $7.50
                } elseif ($totalQuantity >= 2) {
                    $shippingPrice = 1200; // $12.00
                }
            }
            // else: shipping is free (remains 0)
            
            ShippingManifest::addOption(
                new ShippingOption(
                    name: 'Standard Shipping',
                    description: $shippingPrice === 0 ? 'Free Shipping' : 'Standard Shipping',
                    identifier: 'STANDARD',
                    price: new Price($shippingPrice, $cart->currency, 1),
                    taxClass: \Lunar\Models\TaxClass::getDefault()
                )
            );
        }

        return $next($cart);
    }
}
