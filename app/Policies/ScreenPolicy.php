<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Screen;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ScreenPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        // Filtragem feita em getEloquentQuery(); qualquer utilizador autenticado pode aceder à lista
        return true;
    }

    public function view(AuthUser $authUser, Screen $screen): bool
    {
        if ($authUser->can('View:Screen')) {
            return true;
        }

        if ($authUser->id === $screen->user_id) {
            return true;
        }

        return $screen->allowedUsers()->where('users.id', $authUser->id)->exists();
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, Screen $screen): bool
    {
        if ($authUser->can('Update:Screen')) {
            return true;
        }

        return $authUser->id === $screen->user_id;
    }

    public function delete(AuthUser $authUser, Screen $screen): bool
    {
        if ($authUser->can('Delete:Screen')) {
            return true;
        }

        return $authUser->id === $screen->user_id;
    }

    public function restore(AuthUser $authUser, Screen $screen): bool
    {
        return $authUser->can('Restore:Screen');
    }

    public function forceDelete(AuthUser $authUser, Screen $screen): bool
    {
        return $authUser->can('ForceDelete:Screen');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Screen');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Screen');
    }

    public function replicate(AuthUser $authUser, Screen $screen): bool
    {
        return $authUser->can('Replicate:Screen');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Screen');
    }
}
