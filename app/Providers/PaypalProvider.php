<?php

namespace App\Providers;

use App\PaymentTypes\PaypalPayment;
use Illuminate\Support\ServiceProvider;
use GetCandy\Facades\Payments;
use GetCandy\Stripe\Components\PaymentForm;
use GetCandy\Stripe\Managers\StripeManager;
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

        Blade::directive('paypalScripts', function () {
            $paypalClientId = config('paypal.' . config('paypal.mode', 'sandbox') . '.client_id');
            return  <<<EOT
                <script src="https://www.paypal.com/sdk/js?client-id=$paypalClientId"></script>
            EOT;
        });

        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'getcandy');

        // $this->mergeConfigFrom(__DIR__."/../config/stripe.php", "getcandy.stripe");

        // $this->publishes([
        //     __DIR__."/../config/stripe.php" => config_path("getcandy/stripe.php"),
        // ], 'getcandy.stripe.config');

        // $this->publishes([
        //     __DIR__.'/../resources/views' => resource_path('views/vendor/getcandy'),
        // ], 'getcandy.stripe.components');

        // Register the stripe payment component.
        // Livewire::component('stripe.payment', PaymentForm::class);
    }
}