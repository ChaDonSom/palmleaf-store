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
        // Override Lunar's ProductResource with our custom one before the panel is registered
        $reflection = new \ReflectionClass(\Lunar\Admin\LunarPanelManager::class);
        $resourcesProperty = $reflection->getProperty('resources');
        $resourcesProperty->setAccessible(true);
        $resources = $resourcesProperty->getValue();

        // Replace the ProductResource with our custom one
        $resources = array_map(function ($resource) {
            if ($resource === \Lunar\Admin\Filament\Resources\ProductResource::class) {
                return \App\Filament\Resources\ProductResource::class;
            }
            return $resource;
        }, $resources);

        $resourcesProperty->setValue(null, $resources);

        LunarPanel::panel(
            fn($panel) => $panel->plugins([
                new ShippingPlugin,
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
