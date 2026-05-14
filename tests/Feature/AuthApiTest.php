<?php

namespace Tests\Feature;

use App\Enums\WorkspaceRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_default_workspace(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Aadil Hussain',
            'email' => 'aadil@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.email', 'aadil@example.com')
            ->assertJsonPath('workspace.name', "Aadil Hussain's Workspace")
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('user_workspace', [
            'role' => WorkspaceRole::Owner->value,
        ]);
    }

    public function test_register_validates_duplicate_email(): void
    {
        User::factory()->create(['email' => 'aadil@example.com']);

        $this->postJson('/api/v1/register', [
            'name' => 'Aadil Hussain',
            'email' => 'aadil@example.com',
            'password' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'aadil@example.com',
            'password' => 'password123',
        ]);

        $this->postJson('/api/v1/login', [
            'email' => 'aadil@example.com',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonStructure(['user', 'token']);
    }
}
