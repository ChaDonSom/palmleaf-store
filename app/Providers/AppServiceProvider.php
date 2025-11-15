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
                ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        )
            ->extensions([
                \Lunar\Admin\Filament\Pages\Dashboard::class => \App\Filament\Extensions\DashboardExtension::class,
            ])
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
    }
}
