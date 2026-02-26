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
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Filament\RelationManagers\AuditsRelationManager;

class UserResource extends Resource
{
    use HasShieldFormComponents;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Usuários';

    protected static ?string $modelLabel = 'Usuário';

    protected static ?string $pluralModelLabel = 'Usuários';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->email,
        ];
    }

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

    public static function duplicateAction(): Action
    {
        return Action::make('duplicate')
            ->label('Duplicar')
            ->icon('heroicon-o-document-duplicate')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Duplicar usuário')
            ->modalDescription('Um novo usuário será criado com os mesmos roles e permissões diretas deste.')
            ->modalSubmitActionLabel('Duplicar')
            ->action(function (Model $record): void {
                [$localPart, $domain] = explode('@', $record->email, 2);
                $baseEmail = 'copia.' . $localPart . '@' . $domain;
                $newEmail = $baseEmail;
                $counter = 1;

                while (User::where('email', $newEmail)->exists()) {
                    $newEmail = 'copia' . $counter . '.' . $localPart . '@' . $domain;
                    $counter++;
                }

                $newUser = User::create([
                    'name'     => 'Cópia de ' . $record->name,
                    'email'    => $newEmail,
                    'password' => Hash::make(Str::random(16)),
                ]);

                $newUser->syncRoles($record->roles);

                $guard = Utils::getFilamentAuthGuard();
                $directPermissions = $record->getDirectPermissions();

                if ($directPermissions->isNotEmpty()) {
                    $permissionModels = collect();
                    $directPermissions->each(function ($permission) use ($permissionModels, $guard) {
                        $permissionModels->push(
                            Utils::getPermissionModel()::firstOrCreate([
                                'name'       => $permission->name,
                                'guard_name' => $guard,
                            ])
                        );
                    });

                    $newUser->syncPermissions($permissionModels);
                }

                Notification::make()
                    ->title('Usuário duplicado com sucesso!')
                    ->body('O usuário "' . $newUser->name . '" foi criado com os mesmos roles e permissões.')
                    ->success()
                    ->send();
            });
    }

    public static function getRelations(): array
    {
        return [
            AuditsRelationManager::class,
        ];
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
