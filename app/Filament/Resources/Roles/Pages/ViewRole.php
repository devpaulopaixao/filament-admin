<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected static ?string $title = 'Visualizar perfil';

    protected function getActions(): array
    {
        return [
            RoleResource::duplicateAction(),
            EditAction::make(),
        ];
    }
}
