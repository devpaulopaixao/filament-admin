<?php

namespace App\Filament\Resources\Screens\Pages;

use App\Filament\Resources\Screens\ScreenResource;
use App\Filament\Resources\Screens\Widgets\ScreenAccessChartWidget;
use App\Filament\Resources\Screens\Widgets\ScreenAccessStatsWidget;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
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

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getInfolistContentComponent(),
            Livewire::make(ScreenAccessStatsWidget::class, ['record' => $this->getRecord()])->key('stats'),
            Livewire::make(ScreenAccessChartWidget::class, ['record' => $this->getRecord()])->key('chart'),
            $this->getRelationManagersContentComponent(),
        ]);
    }
}
