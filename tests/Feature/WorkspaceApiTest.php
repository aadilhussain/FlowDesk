<?php

namespace Tests\Feature;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_workspaces(): void
    {
        $this->getJson('/api/v1/workspaces')->assertUnauthorized();
    }

    public function test_user_can_create_workspace(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/workspaces', [
            'name' => 'Acme Operations',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Acme Operations')
            ->assertJsonPath('data.owner_id', $user->id);

        $workspace = Workspace::first();

        $this->assertDatabaseHas('user_workspace', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => WorkspaceRole::Owner->value,
        ]);
    }

    public function test_user_only_lists_their_workspaces(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Visible Workspace']);
        $workspace->users()->attach($user->id, ['role' => WorkspaceRole::Owner->value]);
        Workspace::factory()->create(['name' => 'Hidden Workspace']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/workspaces')
            ->assertOk()
            ->assertJsonPath('data.0.id', $workspace->id)
            ->assertJsonMissing(['name' => 'Hidden Workspace']);
    }

    public function test_non_member_cannot_view_workspace(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/workspaces/{$workspace->id}")
            ->assertForbidden();
    }

    public function test_workspace_owner_can_update_and_delete_workspace(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->for($user, 'owner')->create();
        $workspace->users()->attach($user->id, ['role' => WorkspaceRole::Owner->value]);

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/workspaces/{$workspace->id}", ['name' => 'Updated Workspace'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Workspace');

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/workspaces/{$workspace->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('workspaces', [
            'id' => $workspace->id,
        ]);
    }
}
