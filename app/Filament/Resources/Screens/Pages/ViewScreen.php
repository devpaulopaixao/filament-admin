<?php

namespace App\Filament\Resources\Screens\Pages;

use App\Filament\Resources\Screens\ScreenResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScreen extends ViewRecord
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
