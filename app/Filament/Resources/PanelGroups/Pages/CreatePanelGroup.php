<?php

namespace App\Filament\Resources\PanelGroups\Pages;

use App\Filament\Resources\PanelGroups\PanelGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePanelGroup extends CreateRecord
{
    protected static string $resource = PanelGroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
