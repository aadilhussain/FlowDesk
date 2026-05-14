<?php

namespace App\Http\Controllers\Api;

use App\Enums\WorkspaceRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        $workspace = Workspace::create([
            'owner_id' => $user->id,
            'name' => $user->name."'s Workspace",
            'slug' => Str::slug($user->name.'-'.$user->id.'-workspace'),
        ]);

        $workspace->users()->attach($user->id, [
            'role' => WorkspaceRole::Owner->value,
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'workspace' => $workspace,
            'token' => $token,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
