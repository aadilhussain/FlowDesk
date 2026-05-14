<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Enums\WorkspaceRole;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_workspace_tasks(): void
    {
        $workspace = Workspace::factory()->create();

        $this->getJson("/api/v1/workspaces/{$workspace->id}/tasks")->assertUnauthorized();
    }

    public function test_workspace_member_can_create_task(): void
    {
        [$user, $workspace] = $this->workspaceForUser();

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/workspaces/{$workspace->id}/tasks", [
            'title' => 'Prepare sprint plan',
            'description' => 'Write the first version of the sprint task list.',
            'status' => TaskStatus::InProgress->value,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Prepare sprint plan')
            ->assertJsonPath('data.status', TaskStatus::InProgress->value)
            ->assertJsonPath('data.workspace_id', $workspace->id)
            ->assertJsonPath('data.user_id', $user->id);

        $this->assertDatabaseHas('tasks', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'title' => 'Prepare sprint plan',
            'status' => TaskStatus::InProgress->value,
        ]);
    }

    public function test_non_member_cannot_create_workspace_task(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson("/api/v1/workspaces/{$workspace->id}/tasks", [
            'title' => 'Prepare sprint plan',
        ])->assertForbidden();
    }

    public function test_task_status_must_be_valid(): void
    {
        [$user, $workspace] = $this->workspaceForUser();

        $this->actingAs($user, 'sanctum')->postJson("/api/v1/workspaces/{$workspace->id}/tasks", [
            'title' => 'Prepare sprint plan',
            'status' => 'blocked',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_workspace_member_sees_workspace_tasks(): void
    {
        [$user, $workspace] = $this->workspaceForUser();
        $otherWorkspace = Workspace::factory()->create();
        $task = Task::factory()->for($user)->for($workspace)->create(['title' => 'Owned task']);
        Task::factory()->for($user)->for($otherWorkspace)->create(['title' => 'Hidden task']);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/workspaces/{$workspace->id}/tasks");

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $task->id)
            ->assertJsonMissing(['title' => 'Hidden task']);
    }

    public function test_workspace_member_can_view_task_in_workspace(): void
    {
        [$user, $workspace] = $this->workspaceForUser();
        $task = Task::factory()->for($user)->for($workspace)->create();

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/workspaces/{$workspace->id}/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $task->id);
    }

    public function test_user_can_update_their_own_workspace_task(): void
    {
        [$user, $workspace] = $this->workspaceForUser();
        $task = Task::factory()->for($user)->for($workspace)->create([
            'status' => TaskStatus::Pending,
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/workspaces/{$workspace->id}/tasks/{$task->id}", [
            'title' => 'Updated task',
            'status' => TaskStatus::Completed->value,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated task')
            ->assertJsonPath('data.status', TaskStatus::Completed->value);
    }

    public function test_user_cannot_manage_another_users_task(): void
    {
        [$user, $workspace] = $this->workspaceForUser();
        $otherUser = User::factory()->create();
        $workspace->users()->attach($otherUser->id, ['role' => WorkspaceRole::Member->value]);
        $task = Task::factory()->for($otherUser)->for($workspace)->create();

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/workspaces/{$workspace->id}/tasks/{$task->id}", ['title' => 'Nope'])
            ->assertForbidden();

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/workspaces/{$workspace->id}/tasks/{$task->id}")
            ->assertForbidden();
    }

    public function test_task_must_belong_to_route_workspace(): void
    {
        [$user, $workspace] = $this->workspaceForUser();
        $otherWorkspace = Workspace::factory()->create();
        $task = Task::factory()->for($user)->for($otherWorkspace)->create();

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/workspaces/{$workspace->id}/tasks/{$task->id}")
            ->assertNotFound();
    }

    public function test_user_can_delete_their_own_workspace_task(): void
    {
        [$user, $workspace] = $this->workspaceForUser();
        $task = Task::factory()->for($user)->for($workspace)->create();

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/workspaces/{$workspace->id}/tasks/{$task->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * @return array{User, Workspace}
     */
    private function workspaceForUser(?User $user = null): array
    {
        $user ??= User::factory()->create();
        $workspace = Workspace::factory()->for($user, 'owner')->create();

        $workspace->users()->attach($user->id, [
            'role' => WorkspaceRole::Owner->value,
        ]);

        return [$user, $workspace];
    }
}
