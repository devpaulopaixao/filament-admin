<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestPanelsWidget;
use App\Filament\Widgets\LatestScreensWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            LatestPanelsWidget::class,
            LatestScreensWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
