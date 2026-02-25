<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditUser extends EditRecord
{
    public $permissions;

    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Editar usuário';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            UserResource::duplicateAction(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $nonPermissionKeys = ['name', 'email', 'password', 'roles', 'select_all'];

        $this->permissions = collect($data)
            ->filter(function ($permission, $key) use ($nonPermissionKeys) {
                return ! in_array($key, $nonPermissionKeys);
            })
            ->values()
            ->flatten()
            ->filter()
            ->unique();

        return Arr::only($data, ['name', 'email', 'email_verified_at', 'password']);
    }

    protected function afterSave(): void
    {
        $guard = Utils::getFilamentAuthGuard();

        $permissionModels = collect();
        $this->permissions->each(function ($permission) use ($permissionModels, $guard) {
            $permissionModels->push(
                Utils::getPermissionModel()::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guard,
                ])
            );
        });

        $this->record->syncPermissions($permissionModels);
    }
}
