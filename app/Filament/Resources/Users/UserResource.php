<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    use HasShieldFormComponents;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /**
     * Override para carregar apenas permissões diretas do utilizador,
     * excluindo as herdadas por roles.
     */
    public static function setPermissionStateForRecordPermissions(
        Component $component,
        string $operation,
        array $permissions,
        ?Model $record
    ): void {
        if (in_array($operation, ['edit', 'view'])) {
            if (blank($record)) {
                return;
            }

            if ($component->isVisible() && $permissions !== []) {
                $component->state(
                    collect($permissions)
                        ->filter(function ($_value, $key) use ($record) {
                            return $record->hasDirectPermission($key);
                        })
                        ->keys()
                        ->toArray()
                );
            }
        }
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(
            array_merge(
                UserForm::components(),
                [
                    Section::make('Roles')
                        ->schema([
                            Select::make('roles')
                                ->label('Roles')
                                ->multiple()
                                ->relationship('roles', 'name')
                                ->searchable()
                                ->preload(),
                        ])
                        ->columnSpanFull(),

                    Section::make('Permissões diretas')
                        ->schema([
                            static::getSelectAllFormComponent(),
                            static::getShieldFormComponents(),
                        ])
                        ->columnSpanFull(),
                ]
            )
        );
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
