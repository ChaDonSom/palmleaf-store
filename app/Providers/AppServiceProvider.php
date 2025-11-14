<?php

namespace App\Providers;

use App\Modifiers\OrderModifier;
use App\Modifiers\ShippingModifier;
use App\View\Composers\FooterComposer;
use Illuminate\Support\Facades\View;
use Lunar\Base\OrderModifiers;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Base\ShippingModifiers;
use Lunar\Shipping\ShippingPlugin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        LunarPanel::panel(
            fn($panel) => $panel
                ->plugins([
                    new ShippingPlugin,
                ])
                ->resources([
                    ...\Lunar\Admin\LunarPanelManager::getResources(),
                    \App\Filament\Resources\TriviaQuestionResource::class,
                ])
        )
            ->register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(ShippingModifiers $shippingModifiers, OrderModifiers $orderModifiers): void
    {
        \Lunar\Facades\Telemetry::optOut();

        $shippingModifiers->add(
            ShippingModifier::class
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\Product::class,
            \App\Models\Product::class,
            // \App\Models\CustomProduct::class,
        );

        $orderModifiers->add(
            OrderModifier::class
        );

        // Register view composers
        View::composer('components.footer', FooterComposer::class);

        // Debug: Log customer group pivot data when accessed in production
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\DB::listen(function ($query) {
                if (
                    str_contains($query->sql, 'customer_group_product') ||
                    str_contains($query->sql, 'customer_groups')
                ) {
                    \Illuminate\Support\Facades\Log::info('CustomerGroup Query Debug', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                    ]);
                }
            });
        }
    }
}
