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
            ShippingManifest::addOption(
                new ShippingOption(
                    name: 'Basic Delivery',
                    description: 'Basic Delivery',
                    identifier: 'BASDEL',
                    price: new Price(500, $cart->currency, 1),
                    taxClass: \Lunar\Models\TaxClass::getDefault()
                )
            );

            ShippingManifest::addOption(
                new ShippingOption(
                    name: 'Express Delivery',
                    description: 'Express Delivery',
                    identifier: 'EXPDEL',
                    price: new Price(1000, $cart->currency, 1),
                    taxClass: \Lunar\Models\TaxClass::getDefault()
                )
            );
        }

        return $next($cart);
    }
}
