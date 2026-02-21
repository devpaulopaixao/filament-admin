<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PanelGroup;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PanelGroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        // Filtragem feita em getEloquentQuery(); qualquer utilizador autenticado pode aceder à lista
        return true;
    }

    public function view(AuthUser $authUser, PanelGroup $panelGroup): bool
    {
        if ($authUser->can('View:PanelGroup')) {
            return true;
        }

        if ($authUser->id === $panelGroup->user_id) {
            return true;
        }

        return $panelGroup->allowedUsers()->where('users.id', $authUser->id)->exists();
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, PanelGroup $panelGroup): bool
    {
        if ($authUser->can('Update:PanelGroup')) {
            return true;
        }

        return $authUser->id === $panelGroup->user_id;
    }

    public function delete(AuthUser $authUser, PanelGroup $panelGroup): bool
    {
        if ($authUser->can('Delete:PanelGroup')) {
            return true;
        }

        return $authUser->id === $panelGroup->user_id;
    }

    public function restore(AuthUser $authUser, PanelGroup $panelGroup): bool
    {
        return $authUser->can('Restore:PanelGroup');
    }

    public function forceDelete(AuthUser $authUser, PanelGroup $panelGroup): bool
    {
        return $authUser->can('ForceDelete:PanelGroup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PanelGroup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PanelGroup');
    }

    public function replicate(AuthUser $authUser, PanelGroup $panelGroup): bool
    {
        return $authUser->can('Replicate:PanelGroup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PanelGroup');
    }
}
