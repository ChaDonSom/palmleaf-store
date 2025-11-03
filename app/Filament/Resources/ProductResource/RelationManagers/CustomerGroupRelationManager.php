<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Lunar\Admin\Filament\Resources\ProductResource\RelationManagers\CustomerGroupRelationManager as BaseCustomerGroupRelationManager;

class CustomerGroupRelationManager extends BaseCustomerGroupRelationManager
{
    public function getDefaultTable(Table $table): Table
    {
        $pivotColumns = collect($this->getPivotColumns())->map(function ($column) {
            return Tables\Columns\IconColumn::make($column)->label(
                __("lunarpanel::relationmanagers.customer_groups.table.{$column}.label")
            )
                ->color(function ($state) use ($column): string {
                    // Log for debugging in production
                    Log::info("CustomerGroup pivot column '{$column}' state:", [
                        'value' => $state,
                        'type' => gettype($state),
                        'is_empty' => empty($state),
                    ]);

                    // Handle various possible values safely
                    return match ((string) $state) {
                        '1', 'true', 'yes' => 'success',
                        '0', 'false', 'no' => 'warning',
                        default => 'gray', // Handle empty or unexpected values
                    };
                })
                ->icon(function ($state): string {
                    // Handle various possible values safely
                    return match ((string) $state) {
                        '0', 'false', 'no', '' => 'heroicon-o-x-circle',
                        '1', 'true', 'yes' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    };
                });
        })->toArray();

        return parent::getDefaultTable($table)->columns([
            ...[
                Tables\Columns\TextColumn::make('name')->label(
                    __('lunarpanel::relationmanagers.customer_groups.table.name.label')
                ),
            ],
            ...$pivotColumns,
            ...[
                Tables\Columns\TextColumn::make('starts_at')->label(
                    __('lunarpanel::relationmanagers.customer_groups.table.starts_at.label')
                )->dateTime(),
                Tables\Columns\TextColumn::make('ends_at')->label(
                    __('lunarpanel::relationmanagers.customer_groups.table.ends_at.label')
                )->dateTime(),
            ],
        ]);
    }
}
