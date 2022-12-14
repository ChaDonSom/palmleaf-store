<?php

namespace App\Providers;

use App\Modifiers\OrderModifier;
use App\Modifiers\ShippingModifier;
use Lunar\Base\OrderModifiers;
use Lunar\Base\ShippingModifiers;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(ShippingModifiers $shippingModifiers, OrderModifiers $orderModifiers)
    {
        $shippingModifiers->add(
            ShippingModifier::class
        );

        $orderModifiers->add(
            OrderModifier::class
        );
    }
}
