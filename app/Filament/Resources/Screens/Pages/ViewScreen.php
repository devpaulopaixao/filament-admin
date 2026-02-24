<?php

namespace App\Filament\Resources\Screens\Pages;

use App\Filament\Resources\Screens\ScreenResource;
use App\Filament\Resources\Screens\Widgets\ScreenAccessChartWidget;
use App\Filament\Resources\Screens\Widgets\ScreenAccessStatsWidget;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScreen extends ViewRecord
{
    protected static string $resource = ScreenResource::class;

    protected static ?string $title = 'Visualizar tela';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ScreenAccessStatsWidget::class,
            ScreenAccessChartWidget::class,
        ];
    }
}
