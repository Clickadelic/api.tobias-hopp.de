<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
	public function register(RegisterRequest $request): JsonResponse
	{
		$user = User::create($request->validated());
		$user->sendEmailVerificationNotification();

		$token = $user->createToken('api-token')->plainTextToken;

		return response()->json([
			'message' => 'Registered successfully',
			'user' => $user,
			'token' => $token,
		], 201);
	}

	public function resendVerification(Request $request): JsonResponse
	{
		$user = $request->user();

		if ($user->hasVerifiedEmail()) {
			return response()->json([
				'message' => 'Email address is already verified.',
			], 422);
		}

		$user->sendEmailVerificationNotification();

		return response()->json([
			'message' => 'Verification email sent.',
		]);
	}

	public function login(LoginRequest $request): JsonResponse
	{
		$data = $request->validated();

		$user = User::where('email', $data['email'])->first();

		if (! $user || ! Hash::check($data['password'], $user->password)) {
			return response()->json(['message' => 'Invalid credentials'], 401);
		}

		$token = $user->createToken('api-token')->plainTextToken;

		return response()->json([
			'message' => 'Logged in successfully',
			'user' => $user,
			'token' => $token,
		]);
	}

	public function logout(Request $request): JsonResponse
	{
		$request->user()->currentAccessToken()?->delete();

		return response()->json([
			'message' => 'Logged out successfully',
		]);
	}

	public function logoutAll(Request $request): JsonResponse
	{
		$request->user()->tokens()->delete();

		return response()->json([
			'message' => 'Logged out from all devices successfully',
		]);
	}
}
