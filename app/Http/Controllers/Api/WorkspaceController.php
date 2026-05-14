<?php

namespace App\Http\Controllers\Api;

use App\Enums\WorkspaceRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the authenticated user's workspaces.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Workspace::class);

        $workspaces = $request->user()
            ->workspaces()
            ->latest('workspaces.created_at')
            ->paginate($request->integer('per_page', 15));

        return WorkspaceResource::collection($workspaces);
    }

    /**
     * Store a newly created workspace.
     */
    public function store(StoreWorkspaceRequest $request): JsonResponse
    {
        Gate::authorize('create', Workspace::class);

        $workspace = Workspace::create([
            'owner_id' => $request->user()->id,
            'name' => $request->validated('name'),
            'slug' => $this->uniqueSlug($request->validated('name')),
        ]);

        $workspace->users()->attach($request->user()->id, [
            'role' => WorkspaceRole::Owner->value,
        ]);

        return (new WorkspaceResource($workspace))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified workspace.
     */
    public function show(Workspace $workspace): WorkspaceResource
    {
        Gate::authorize('view', $workspace);

        return new WorkspaceResource($workspace);
    }

    /**
     * Update the specified workspace.
     */
    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): WorkspaceResource
    {
        Gate::authorize('update', $workspace);

        $workspace->update([
            'name' => $request->validated('name'),
        ]);

        return new WorkspaceResource($workspace->refresh());
    }

    /**
     * Remove the specified workspace.
     */
    public function destroy(Workspace $workspace): JsonResponse
    {
        Gate::authorize('delete', $workspace);

        $workspace->delete();

        return response()->json(null, 204);
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (Workspace::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
