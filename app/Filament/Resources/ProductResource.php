<?php

namespace App\Filament\Resources;

use Filament\Resources\RelationManagers\RelationGroup;
use Lunar\Admin\Filament\Resources\ProductResource as BaseProductResource;
use Lunar\Admin\Support\RelationManagers\ChannelRelationManager;
use Lunar\Admin\Support\RelationManagers\MediaRelationManager;
use Lunar\Admin\Support\RelationManagers\PriceRelationManager;
use Lunar\Admin\Filament\Resources\ProductResource\RelationManagers\CustomerGroupPricingRelationManager;

class ProductResource extends BaseProductResource
{
    public static function getDefaultRelations(): array
    {
        return [
            RelationGroup::make('Availability', [
                ChannelRelationManager::class,
                // Use our custom CustomerGroupRelationManager instead of Lunar's
                \App\Filament\Resources\ProductResource\RelationManagers\CustomerGroupRelationManager::class,
            ]),
            MediaRelationManager::class,
            PriceRelationManager::class,
            CustomerGroupPricingRelationManager::class,
        ];
    }
}
