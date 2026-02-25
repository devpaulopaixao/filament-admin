<?php

namespace App\Filament\Resources\PanelGroups\Pages;

use App\Filament\Resources\PanelGroups\PanelGroupResource;
use App\Filament\Widgets\PanelGroupsInfoWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPanelGroups extends ListRecords
{
    protected static string $resource = PanelGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PanelGroupsInfoWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
