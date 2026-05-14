<?php

namespace App\Models;

use App\Enums\WorkspaceRole;
use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['owner_id', 'name', 'slug'])]
class Workspace extends Model
{
    /** @use HasFactory<WorkspaceFactory> */
    use HasFactory;

    /**
     * Get the user that owns the workspace.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the users that belong to the workspace.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the workspace tasks.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function hasMember(User $user): bool
    {
        return $this->users()
            ->whereKey($user->id)
            ->exists();
    }

    public function hasAdmin(User $user): bool
    {
        return $this->users()
            ->whereKey($user->id)
            ->wherePivotIn('role', [
                WorkspaceRole::Owner->value,
                WorkspaceRole::Admin->value,
            ])
            ->exists();
    }
}
