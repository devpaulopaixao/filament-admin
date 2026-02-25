<?php

namespace App\Filament\Resources\Screens\Pages;

use App\Filament\Resources\Screens\ScreenResource;
use App\Filament\Resources\Screens\Widgets\ScreenAccessChartWidget;
use App\Filament\Resources\Screens\Widgets\ScreenAccessStatsWidget;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewScreen extends ViewRecord
{
    protected static string $resource = ScreenResource::class;

    protected static ?string $title = 'Visualizar tela';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_display')
                ->label('Ver exibição')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('success')
                ->url(url('/tela/' . $this->getRecord()->id))
                ->openUrlInNewTab(),
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
