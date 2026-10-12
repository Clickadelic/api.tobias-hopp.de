<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Email Verification',
    description: 'Endpoints for email verification'
)]
class EmailVerificationController extends Controller
{
    public function verify(Request $request, string $id, string $hash): JsonResponse|View
    {
        $user = User::find($id);

        if (! $user || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            if ($request->wantsJson()) {
                abort(403);
            }

            return response()->view('auth.email-verification-failed', [], 403);
        }

        $alreadyVerified = $user->hasVerifiedEmail();

        if (! $alreadyVerified) {
            $user->markEmailAsVerified();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Email address verified successfully.',
                'email_verified' => true,
            ]);
        }

        return view('auth.email-verified', [
            'user' => $user,
            'alreadyVerified' => $alreadyVerified,
        ]);
    }
}
