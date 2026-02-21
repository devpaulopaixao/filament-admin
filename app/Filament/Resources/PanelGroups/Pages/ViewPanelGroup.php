<?php

namespace App\Filament\Resources\PanelGroups\Pages;

use App\Filament\Resources\PanelGroups\PanelGroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPanelGroup extends ViewRecord
{
    protected static string $resource = PanelGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
