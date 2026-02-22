<?php

namespace App\Filament\Resources\Screens;

use App\Filament\Resources\Screens\Pages\CreateScreen;
use App\Filament\Resources\Screens\Pages\EditScreen;
use App\Filament\Resources\Screens\Pages\ListScreens;
use App\Filament\Resources\Screens\Pages\ViewScreen;
use App\Filament\Resources\Screens\RelationManagers\AllowedUsersRelationManager;
use App\Filament\Resources\Screens\Schemas\ScreenForm;
use App\Filament\Resources\Screens\Schemas\ScreenInfolist;
use App\Filament\Resources\Screens\Tables\ScreensTable;
use App\Models\Screen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScreenResource extends Resource
{
    protected static ?string $model = Screen::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTv;

    protected static ?string $navigationLabel = 'Telas';

    protected static ?string $modelLabel = 'Tela';

    protected static ?string $pluralModelLabel = 'Telas';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->can('ViewAny:Screen')) {
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
        return ScreenForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScreenInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScreensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AllowedUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListScreens::route('/'),
            'create' => CreateScreen::route('/create'),
            'view'   => ViewScreen::route('/{record}'),
            'edit'   => EditScreen::route('/{record}/edit'),
        ];
    }
}
