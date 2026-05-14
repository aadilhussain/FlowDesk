<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        return $this->belongsToWorkspace($user, $task);
    }

    /**
     * Determine whether the user can create tasks.
     */
    public function create(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        return $this->ownsTask($user, $task) && $this->belongsToWorkspace($user, $task);
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $this->ownsTask($user, $task) && $this->belongsToWorkspace($user, $task);
    }

    private function ownsTask(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    private function belongsToWorkspace(User $user, Task $task): bool
    {
        return $task->workspace?->hasMember($user) ?? false;
    }
}
