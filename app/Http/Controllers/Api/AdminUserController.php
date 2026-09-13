<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
	public function index(): JsonResponse
	{
		return response()->json([
			'users' => UserResource::collection(User::query()->orderBy('name')->get()),
		]);
	}

	public function updateRole(Request $request, User $user): UserResource
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
