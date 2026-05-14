<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the authenticated user's tasks.
     */
    public function index(Request $request, Workspace $workspace)
    {
        Gate::authorize('viewAny', [Task::class, $workspace]);

        $tasks = $workspace
            ->tasks()
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request, Workspace $workspace): JsonResponse
    {
        Gate::authorize('create', [Task::class, $workspace]);

        $task = $workspace->tasks()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified task.
     */
    public function show(Workspace $workspace, Task $task): TaskResource
    {
        $this->ensureTaskBelongsToWorkspace($workspace, $task);
        Gate::authorize('view', $task);

        return new TaskResource($task);
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Workspace $workspace, Task $task): TaskResource
    {
        $this->ensureTaskBelongsToWorkspace($workspace, $task);
        Gate::authorize('update', $task);

        $task->update($request->validated());

        return new TaskResource($task->refresh());
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Workspace $workspace, Task $task): JsonResponse
    {
        $this->ensureTaskBelongsToWorkspace($workspace, $task);
        Gate::authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }

    private function ensureTaskBelongsToWorkspace(Workspace $workspace, Task $task): void
    {
        abort_if($task->workspace_id !== $workspace->id, 404);
    }
}
