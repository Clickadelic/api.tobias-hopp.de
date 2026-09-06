<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends Controller
{
	public function verify(string $id, string $hash): JsonResponse
	{
		$user = User::findOrFail($id);

		abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

		if (! $user->hasVerifiedEmail()) {
			$user->markEmailAsVerified();
		}

		return response()->json([
			'message' => 'Email address verified successfully.',
			'email_verified' => true,
		]);
	}
}
