<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateUser extends CreateRecord
{
    public $permissions;

    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $nonPermissionKeys = ['name', 'email', 'email_verified_at', 'password', 'roles', 'select_all'];

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

    protected function afterCreate(): void
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
