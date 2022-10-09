<?php

namespace App\Providers;

use App\Http\Livewire\PaypalPaymentForm;
use App\PaymentTypes\PaypalPayment;
use Illuminate\Support\ServiceProvider;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Stripe\Managers\StripeManager;
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
        // Register our payment type.
        Payments::extend('paypal', function ($app) {
            return $app->make(PaypalPayment::class);
        });

        // $this->app->singleton('paypal', function ($app) {
        //     return $app->make(PaypalManager::class); // A way to get the stripe client, seems like
        // });

        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'lunar');

        // $this->mergeConfigFrom(__DIR__."/../config/stripe.php", "lunar.stripe");

        // $this->publishes([
        //     __DIR__."/../config/stripe.php" => config_path("lunar/stripe.php"),
        // ], 'lunar.stripe.config');

        // $this->publishes([
        //     __DIR__.'/../resources/views' => resource_path('views/vendor/lunar'),
        // ], 'lunar.stripe.components');

        // Register the stripe payment component.
        Livewire::component('paypal.payment', PaypalPaymentForm::class);
    }
}