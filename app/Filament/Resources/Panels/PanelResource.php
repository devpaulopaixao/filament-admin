<?php

namespace App\Filament\Resources\Panels;

use App\Filament\Resources\Panels\Pages\CreatePanel;
use App\Filament\Resources\Panels\Pages\EditPanel;
use App\Filament\Resources\Panels\Pages\ListPanels;
use App\Filament\Resources\Panels\Pages\ViewPanel;
use App\Filament\Resources\Panels\RelationManagers\AllowedUsersRelationManager;
use App\Filament\Resources\Panels\RelationManagers\PanelLinksRelationManager;
use App\Filament\Resources\Panels\Schemas\PanelForm;
use App\Filament\Resources\Panels\Schemas\PanelInfolist;
use App\Filament\Resources\Panels\Tables\PanelsTable;
use App\Models\Panel;
use App\Models\PanelLink;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;

class PanelResource extends Resource
{
    protected static ?string $model = Panel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $navigationLabel = 'Painéis';

    protected static ?string $modelLabel = 'Painel';

    protected static ?string $pluralModelLabel = 'Painéis';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Grupo' => optional($record->panelGroup)->title ?? '—',
            'Status' => $record->status ? 'Ativo' : 'Inativo',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->can('ViewAny:Panel')) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            // Criador
            $q->where('user_id', $user->id)
                // Painel sem grupo com acesso direto
                ->orWhere(function (Builder $q) use ($user) {
                    $q->whereNull('panel_group_id')
                        ->whereHas('allowedUsers', function (Builder $q) use ($user) {
                            $q->where('users.id', $user->id);
                        });
                })
                // Painel com grupo onde o utilizador tem acesso ao grupo
                ->orWhere(function (Builder $q) use ($user) {
                    $q->whereNotNull('panel_group_id')
                        ->whereHas('panelGroup.allowedUsers', function (Builder $q) use ($user) {
                            $q->where('users.id', $user->id);
                        });
                });
        });
    }

    public static function form(Schema $schema): Schema
    {
        return PanelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PanelInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PanelsTable::configure($table);
    }

    public static function duplicateAction(): Action
    {
        return Action::make('duplicate')
            ->label('Duplicar')
            ->icon('heroicon-o-document-duplicate')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Duplicar painel')
            ->modalDescription('Um novo painel será criado com os mesmos links e utilizadores permitidos deste.')
            ->modalSubmitActionLabel('Duplicar')
            ->action(function (Model $record): void {
                $baseName = 'Cópia de ' . $record->title;
                $newTitle = $baseName;
                $counter = 1;

                while (Panel::where('title', $newTitle)->exists()) {
                    $newTitle = $baseName . ' (' . $counter . ')';
                    $counter++;
                }

                $newPanel = Panel::create([
                    'user_id'        => auth()->id(),
                    'panel_group_id' => $record->panel_group_id,
                    'title'          => $newTitle,
                    'status'         => $record->status,
                    'show_controls'  => $record->show_controls,
                ]);

                $record->links->each(function (PanelLink $link) use ($newPanel): void {
                    PanelLink::create([
                        'panel_id'       => $newPanel->id,
                        'title'          => $link->title,
                        'url'            => $link->url,
                        'status'         => $link->status,
                        'duration_time'  => $link->duration_time,
                        'display_number' => $link->display_number,
                    ]);
                });

                $newPanel->allowedUsers()->sync($record->allowedUsers->pluck('id'));

                Notification::make()
                    ->title('Painel duplicado com sucesso!')
                    ->body('O painel "' . $newTitle . '" foi criado com todos os links e acessos.')
                    ->success()
                    ->send();
            });
    }

    public static function getRelations(): array
    {
        return [
            PanelLinksRelationManager::class,
            AllowedUsersRelationManager::class,
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPanels::route('/'),
            'create' => CreatePanel::route('/create'),
            'view'   => ViewPanel::route('/{record}'),
            'edit'   => EditPanel::route('/{record}/edit'),
        ];
    }
}
