<?php

namespace App\Filament\Resources\Panels\Pages;

use App\Filament\Resources\Panels\PanelResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPanel extends EditRecord
{
    protected static string $resource = PanelResource::class;

    protected static ?string $title = 'Editar painel';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_display')
                ->label('Ver exibição')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('success')
                ->url(url('/painel/' . $this->getRecord()->hash))
                ->openUrlInNewTab(),
            ViewAction::make(),
            PanelResource::duplicateAction(),
            DeleteAction::make(),
        ];
    }
}
