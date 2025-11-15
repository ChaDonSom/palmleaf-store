<?php

namespace App\Filament\Widgets;

use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Lunar\Models\Order;

class OrdersAwaitingCaptureWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    protected static ?int $sort = 0; // Show at the top of the dashboard

    protected function getStats(): array
    {
        $awaitingCapture = Order::where('status', 'requires-capture')
            ->whereNull('placed_at')
            ->count();

        $awaitingCaptureTotal = Order::where('status', 'requires-capture')
            ->whereNull('placed_at')
            ->sum('total');

        // Format the total using the first order's currency or default
        $firstOrder = Order::where('status', 'requires-capture')
            ->whereNull('placed_at')
            ->with('currency')
            ->first();

        $formattedTotal = $firstOrder
            ? '$' . number_format($awaitingCaptureTotal / 100, 2)
            : '$0.00';

        return [
            Stat::make(
                label: 'Orders Awaiting Capture',
                value: number_format($awaitingCapture),
            )
                ->description($awaitingCapture > 0 ? "Total value: {$formattedTotal}" : 'No orders pending capture')
                ->descriptionIcon(
                    $awaitingCapture > 0
                        ? FilamentIcon::resolve('heroicon-m-clock')
                        : FilamentIcon::resolve('heroicon-m-check-circle')
                )
                ->color($awaitingCapture > 0 ? 'warning' : 'success')
                ->url(route('filament.lunar.resources.orders.index', [
                    'tableFilters' => [
                        'status' => [
                            'values' => ['requires-capture'],
                        ],
                    ],
                ])),
        ];
    }
}
