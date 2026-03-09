<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa o cache de permissões antes de seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissões por recurso ────────────────────────────────────────────
        $resources = ['Audit', 'Panel', 'PanelGroup', 'Role', 'Screen', 'User'];

        $actions = [
            'View', 'ViewAny',
            'Create',
            'Update',
            'Delete', 'DeleteAny',
            'ForceDelete', 'ForceDeleteAny',
            'Restore', 'RestoreAny',
            'Replicate',
            'Reorder',
        ];

        $allPermissions = [];
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $allPermissions[] = "{$action}:{$resource}";
            }
        }

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Limpa pivot antes de sincronizar (idempotente) ───────────────────
        DB::table('role_has_permissions')->truncate();

        // ── super_admin: acesso total ─────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions($allPermissions);

        // ── panel_user: apenas visualização de painéis e telas ───────────────
        $panelUser = Role::firstOrCreate(['name' => 'panel_user']);
        $panelUser->syncPermissions([
            'View:Panel', 'ViewAny:Panel',
            'View:PanelGroup', 'ViewAny:PanelGroup',
            'View:Screen', 'ViewAny:Screen',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
