<?php

namespace App\Filament\Resources\Screens\Pages;

use App\Filament\Resources\Screens\ScreenResource;
use App\Filament\Widgets\ScreensInfoWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScreens extends ListRecords
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ScreensInfoWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
