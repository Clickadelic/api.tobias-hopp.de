<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Admin Users', description: 'User and role management. Requires the admin role.')]
class AdminUserController extends Controller
{
    #[OA\Get(
        path: '/api/v1/users',
        summary: 'List all users (admin)',
        security: [['sanctum' => []]],
        tags: ['Admin Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All users ordered by name',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'users', type: 'array', items: new OA\Items(ref: '#/components/schemas/User')),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 403, description: 'Not an admin or email not verified'),
        ]
    )]
    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        return response()->json([
            'users' => UserResource::collection(User::query()->orderBy('name')->get()),
        ]);
    }

    #[OA\Post(
        path: '/api/v1/users',
        summary: 'Create a new user (admin)',
        description: 'Creates a user without a password and sends them a password reset email so they can set their own password.',
        security: [['sanctum' => []]],
        tags: ['Admin Users'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', description: "User's full name"),
                    new OA\Property(property: 'email', type: 'string', format: 'email', description: "User's email address"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created and password reset email sent', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/User')])),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 403, description: 'Not an admin or email not verified'),
            new OA\Response(response: 422, description: 'Validation error (email already exists)', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt(str()->random(32)),
        ]);

        $user->syncRoles(['user']);

        $token = Password::createToken($user);

        try {
            $user->sendPasswordResetNotification($token, config('app.frontend_url'));
        } catch (\Throwable) {
            // Silently continue - user is created even if email fails
        }

        return response()->json([
            'data' => new UserResource($user),
        ], 201);
    }

    #[OA\Patch(
        path: '/api/v1/users/{user}/role',
        summary: "Change a user's role (admin)",
        description: 'Admins cannot remove their own admin role.',
        security: [['sanctum' => []]],
        tags: ['Admin Users'],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['role'],
                properties: [new OA\Property(property: 'role', type: 'string', enum: ['user', 'admin'])]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated user', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/User')])),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 403, description: 'Not an admin or email not verified'),
            new OA\Response(response: 404, description: 'User not found'),
            new OA\Response(response: 422, description: 'Validation error or attempt to remove own admin role', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function updateRole(Request $request, User $user): UserResource
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['user', 'admin'])],
        ]);

        Gate::authorize('update', $user);

        $actor = $request->user();

        if ($actor->is($user) && $validated['role'] !== 'admin') {
            abort(422, 'You cannot remove your own admin role.');
        }

        $user->syncRoles([$validated['role']]);

        return new UserResource($user->refresh());
    }

    #[OA\Delete(
        path: '/api/v1/users/{user}',
        summary: 'Delete a user (admin)',
        security: [['sanctum' => []]],
        tags: ['Admin Users'],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deleted successfully', content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string')])),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 403, description: 'Not an admin or email not verified'),
            new OA\Response(response: 404, description: 'User not found'),
            new OA\Response(response: 422, description: 'Attempt to delete own account', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
        ]
    )]
    public function destroy(Request $request, User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        abort_if($request->user()->is($user), 422, 'You cannot delete your own account.');

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
