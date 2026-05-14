<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(TaskStatus::cases())->value,
        ];
    }
}
