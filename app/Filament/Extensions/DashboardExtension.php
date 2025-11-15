<?php

namespace App\Filament\Extensions;

use App\Filament\Widgets\OrdersAwaitingCaptureWidget;
use Lunar\Admin\Support\Extending\BaseExtension;

class DashboardExtension extends BaseExtension
{
    /**
     * Override or add to the overview widgets at the top of the dashboard
     */
    public function getOverviewWidgets(array $widgets): array
    {
        return [
            OrdersAwaitingCaptureWidget::class,
            ...$widgets,
        ];
    }
}
