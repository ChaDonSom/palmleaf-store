<?php

namespace App\Providers;

use App\Livewire\PaypalPaymentForm;
use App\PaymentTypes\PaypalPayment;
use Illuminate\Support\ServiceProvider;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Stripe\Managers\StripeManager;
use Lunar\Stripe\StripePaymentType;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

class PaypalProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register Stripe payment driver
        Payments::extend('stripe', function ($app) {
            return $app->make(StripePaymentType::class);
        });

        // Register PayPal payment driver
        Payments::extend('paypal', function ($app) {
            return $app->make(PaypalPayment::class);
        });

        // Register the Livewire payment components.
        Livewire::component('paypal.payment', PaypalPaymentForm::class);
    }
}
