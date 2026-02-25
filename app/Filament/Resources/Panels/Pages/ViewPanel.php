<?php

namespace App\Filament\Resources\Panels\Pages;

use App\Filament\Resources\Panels\PanelResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPanel extends ViewRecord
{
    protected static string $resource = PanelResource::class;

    protected static ?string $title = 'Visualizar painel';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_display')
                ->label('Ver exibição')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('success')
                ->url(url('/painel/' . $this->getRecord()->hash))
                ->openUrlInNewTab(),
            PanelResource::duplicateAction(),
            EditAction::make(),
        ];
    }
}
