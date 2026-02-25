<?php

namespace App\Filament\Resources\Screens\Pages;

use App\Filament\Resources\Screens\ScreenResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditScreen extends EditRecord
{
    protected static string $resource = ScreenResource::class;

    protected static ?string $title = 'Editar tela';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_display')
                ->label('Ver exibição')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('success')
                ->url(url('/tela/' . $this->getRecord()->id))
                ->openUrlInNewTab(),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
