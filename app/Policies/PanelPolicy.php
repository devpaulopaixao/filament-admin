<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Panel;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PanelPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        // Filtragem feita em getEloquentQuery(); qualquer utilizador autenticado pode aceder à lista
        return true;
    }

    public function view(AuthUser $authUser, Panel $panel): bool
    {
        if ($authUser->can('View:Panel')) {
            return true;
        }

        if ($authUser->id === $panel->user_id) {
            return true;
        }

        // Painel sem grupo: verifica lista própria de utilizadores permitidos
        if (is_null($panel->panel_group_id)) {
            return $panel->allowedUsers()->where('users.id', $authUser->id)->exists();
        }

        // Painel com grupo: acesso herdado do grupo
        return $panel->panelGroup->allowedUsers()->where('users.id', $authUser->id)->exists();
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, Panel $panel): bool
    {
        if ($authUser->can('Update:Panel')) {
            return true;
        }

        return $authUser->id === $panel->user_id;
    }

    public function delete(AuthUser $authUser, Panel $panel): bool
    {
        if ($authUser->can('Delete:Panel')) {
            return true;
        }

        return $authUser->id === $panel->user_id;
    }

    public function restore(AuthUser $authUser, Panel $panel): bool
    {
        return $authUser->can('Restore:Panel');
    }

    public function forceDelete(AuthUser $authUser, Panel $panel): bool
    {
        return $authUser->can('ForceDelete:Panel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Panel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Panel');
    }

    public function replicate(AuthUser $authUser, Panel $panel): bool
    {
        return $authUser->can('Replicate:Panel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Panel');
    }
}
