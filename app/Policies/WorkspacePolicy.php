<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine whether the user can view any workspaces.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the workspace.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }

    /**
     * Determine whether the user can create workspaces.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the workspace.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        return $workspace->hasAdmin($user);
    }

    /**
     * Determine whether the user can delete the workspace.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id;
    }
}
