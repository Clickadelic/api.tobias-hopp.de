<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Admin Users", description: "User and role management. Requires the admin role.")]
class AdminUserController extends Controller
{
	#[OA\Get(
		path: "/api/v1/users",
		summary: "List all users (admin)",
		security: [["sanctum" => []]],
		tags: ["Admin Users"],
		responses: [
			new OA\Response(
				response: 200,
				description: "All users ordered by name",
				content: new OA\JsonContent(properties: [
					new OA\Property(property: "users", type: "array", items: new OA\Items(ref: "#/components/schemas/User")),
				])
			),
			new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 403, description: "Not an admin or email not verified"),
		]
	)]
	public function index(): JsonResponse
	{
		return response()->json([
			'users' => UserResource::collection(User::query()->orderBy('name')->get()),
		]);
	}

	#[OA\Patch(
		path: "/api/v1/users/{user}/role",
		summary: "Change a user's role (admin)",
		description: "Admins cannot remove their own admin role.",
		security: [["sanctum" => []]],
		tags: ["Admin Users"],
		parameters: [
			new OA\Parameter(name: "user", in: "path", required: true, description: "User ID", schema: new OA\Schema(type: "integer")),
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["role"],
				properties: [new OA\Property(property: "role", type: "string", enum: ["user", "admin"])]
			)
		),
		responses: [
			new OA\Response(response: 200, description: "Updated user", content: new OA\JsonContent(properties: [new OA\Property(property: "data", ref: "#/components/schemas/User")])),
			new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 403, description: "Not an admin or email not verified"),
			new OA\Response(response: 404, description: "User not found"),
			new OA\Response(response: 422, description: "Validation error or attempt to remove own admin role", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
		]
	)]	public function updateRole(Request $request, User $user): UserResource
	{
		$validated = $request->validate([
			'role' => ['required', Rule::in(['user', 'admin'])],
		]);

		$actor = $request->user();

		if ($actor->is($user) && $validated['role'] !== 'admin') {
			abort(422, 'You cannot remove your own admin role.');
		}

		$user->syncRoles([$validated['role']]);

		return new UserResource($user->refresh());
	}
}
