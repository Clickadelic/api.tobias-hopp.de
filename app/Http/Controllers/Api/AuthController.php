<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use OpenApi\Attributes as OA;


#[OA\Get(
	path: "/api/auth/*",
	summary: "Endpoints for user authentication and management",
	tags: ["User"],
	responses: [
		new OA\Response(
			response: 200,
			description: "Successful logout of the current user from all devices"
		)
	]
)]

class AuthController extends Controller
{
	#[OA\Get(
		path: "/api/auth/register",
		summary: "Register a new user",
		tags: ["User"],
		responses: [
			new OA\Response(
				response: 201,
				description: "Successful registration of a new user"
			)
		]
	)]
	public function register(RegisterRequest $request): JsonResponse
	{
		$user = User::create($request->validated());
		$user->assignRole(Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']));

		// An undeliverable verification mail shouldn't fail the whole registration.
		try {
			$user->sendEmailVerificationNotification();
		} catch (\Throwable $e) {
			Log::warning('Verification email could not be sent.', ['user_id' => $user->id, 'error' => $e->getMessage()]);
		}

		$token = $user->createToken('api-token')->plainTextToken;

		return response()->json([
			'message' => 'Registered successfully',
			'user' => new UserResource($user),
			'token' => $token,
		], 201);
	}

	#[OA\Get(
		path: "/api/auth/resend-verification",
		summary: "Resend email verification",
		tags: ["User"],
		responses: [
			new OA\Response(
				response: 200,
				description: "Successful resend of email verification"
			)
		]
	)]
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

	#[OA\Get(
		path: "/api/auth/login",
		summary: "Login a user",
		tags: ["User"],
		responses: [
			new OA\Response(
				response: 200,
				description: "Successful login of a user"
			)
		]
	)]
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
			'user' => new UserResource($user),
			'token' => $token,
		]);
	}

	#[OA\Get(
		path: "/api/auth/forgot-password",
		summary: "Request a password reset link",
		tags: ["User"],
		responses: [
			new OA\Response(
				response: 200,
				description: "Successful request for a password reset link"
			)
		]
	)]
	public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
	{
		Password::sendResetLink($request->validated());

		return response()->json([
			'message' => 'If an account exists for that email address, a password reset link has been sent.',
		]);
	}

	#[OA\Get(
		path: "/api/auth/logout",
		summary: "Logout the current user",
		tags: ["User"],
		responses: [
			new OA\Response(
				response: 200,
				description: "Successful logout of the current user"
			)
		]
	)]
	public function logout(Request $request): JsonResponse
	{
		$request->user()->currentAccessToken()?->delete();

		return response()->json([
			'message' => 'Logged out successfully',
		]);
	}

	#[OA\Get(
		path: "/api/auth/logout-all",
		summary: "Logout the current user from all devices",
		tags: ["User"],
		responses: [
			new OA\Response(
				response: 200,
				description: "Successful logout of the current user from all devices"
			)
		]
	)]
	public function logoutAll(Request $request): JsonResponse
	{
		$request->user()->tokens()->delete();

		return response()->json([
			'message' => 'Logged out from all devices successfully',
		]);
	}
}
