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

class AuthController extends Controller
{
	#[OA\Post(
		path: "/api/auth/register",
		summary: "Register a new user",
		tags: ["User"],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["name", "email", "password", "password_confirmation"],
				properties: [
					new OA\Property(property: "name", type: "string", maxLength: 255, example: "Jane Doe"),
					new OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "jane@example.com"),
					new OA\Property(property: "password", type: "string", format: "password", minLength: 8),
					new OA\Property(property: "password_confirmation", type: "string", format: "password"),
				]
			)
		),
		responses: [
			new OA\Response(
				response: 201,
				description: "User registered; a verification email is sent",
				content: new OA\JsonContent(properties: [
					new OA\Property(property: "message", type: "string", example: "Registered successfully"),
					new OA\Property(property: "user", ref: "#/components/schemas/User"),
					new OA\Property(property: "token", type: "string"),
				])
			),
			new OA\Response(response: 422, description: "Validation error", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
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

	#[OA\Post(
		path: "/api/auth/email/verification-notification",
		summary: "Resend the email verification link",
		security: [["sanctum" => []]],
		tags: ["User"],
		responses: [
			new OA\Response(response: 200, description: "Verification email sent", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 422, description: "Email address is already verified", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 429, description: "Too many requests (6 per minute)"),
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

	#[OA\Post(
		path: "/api/auth/login",
		summary: "Login a user",
		tags: ["User"],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["email", "password"],
				properties: [
					new OA\Property(property: "email", type: "string", format: "email", example: "jane@example.com"),
					new OA\Property(property: "password", type: "string", format: "password"),
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Logged in",
				content: new OA\JsonContent(properties: [
					new OA\Property(property: "message", type: "string", example: "Logged in successfully"),
					new OA\Property(property: "user", ref: "#/components/schemas/User"),
					new OA\Property(property: "token", type: "string"),
				])
			),
			new OA\Response(response: 401, description: "Invalid credentials", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 422, description: "Validation error", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
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

	#[OA\Post(
		path: "/api/auth/forgot-password",
		summary: "Request a password reset link",
		tags: ["User"],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["email"],
				properties: [new OA\Property(property: "email", type: "string", format: "email", example: "jane@example.com")]
			)
		),
		responses: [
			new OA\Response(response: 200, description: "Always returned, whether or not the account exists", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 422, description: "Validation error", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
		]
	)]
	public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
	{
		Password::sendResetLink($request->validated());

		return response()->json([
			'message' => 'If an account exists for that email address, a password reset link has been sent.',
		]);
	}

	#[OA\Post(
		path: "/api/auth/logout",
		summary: "Logout the current user (revokes the current token)",
		security: [["sanctum" => []]],
		tags: ["User"],
		responses: [
			new OA\Response(response: 200, description: "Logged out", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 403, description: "Email address not verified"),
		]
	)]
	public function logout(Request $request): JsonResponse
	{
		$request->user()->currentAccessToken()?->delete();

		return response()->json([
			'message' => 'Logged out successfully',
		]);
	}

	#[OA\Post(
		path: "/api/auth/logout-all",
		summary: "Logout the current user from all devices (revokes all tokens)",
		security: [["sanctum" => []]],
		tags: ["User"],
		responses: [
			new OA\Response(response: 200, description: "Logged out everywhere", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 403, description: "Email address not verified"),
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
