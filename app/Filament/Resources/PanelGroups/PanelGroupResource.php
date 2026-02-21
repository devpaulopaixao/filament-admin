<?php

namespace App\Filament\Resources\PanelGroups;

use App\Filament\Resources\PanelGroups\Pages\CreatePanelGroup;
use App\Filament\Resources\PanelGroups\Pages\EditPanelGroup;
use App\Filament\Resources\PanelGroups\Pages\ListPanelGroups;
use App\Filament\Resources\PanelGroups\Pages\ViewPanelGroup;
use App\Filament\Resources\PanelGroups\RelationManagers\PanelsRelationManager;
use App\Filament\Resources\PanelGroups\Schemas\PanelGroupForm;
use App\Filament\Resources\PanelGroups\Schemas\PanelGroupInfolist;
use App\Filament\Resources\PanelGroups\Tables\PanelGroupsTable;
use App\Models\PanelGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PanelGroupResource extends Resource
{
    protected static ?string $model = PanelGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Grupos de Painéis';

    protected static ?string $modelLabel = 'Grupo de Painéis';

    protected static ?string $pluralModelLabel = 'Grupos de Painéis';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->can('ViewAny:PanelGroup')) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhereHas('allowedUsers', function (Builder $q) use ($user) {
                    $q->where('users.id', $user->id);
                });
        });
    }

    public static function form(Schema $schema): Schema
    {
        return PanelGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PanelGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PanelGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PanelsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPanelGroups::route('/'),
            'create' => CreatePanelGroup::route('/create'),
            'view'   => ViewPanelGroup::route('/{record}'),
            'edit'   => EditPanelGroup::route('/{record}/edit'),
        ];
    }
}
