<?php

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BackgroundImageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactSubmissionController;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamInvitationController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\WorkspaceController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
	Route::post('register', [AuthController::class, 'register']);
	Route::post('login', [AuthController::class, 'login']);
	Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
	Route::post('reset-password', [AuthController::class, 'resetPassword']);
	Route::post('email/verification-notification', [AuthController::class, 'resendVerification'])
		->middleware(['auth:sanctum', 'throttle:6,1']);

	Route::middleware(['auth:sanctum', 'verified'])->group(function () {
		Route::post('logout', [AuthController::class, 'logout']);
		Route::post('logout-all', [AuthController::class, 'logoutAll']);
	});
});

Route::prefix('v1')->group(function () {
<<<<<<< HEAD
	// Contact Submissions
	Route::post('contact-submission', [ContactSubmissionController::class, 'store']);
=======
	Route::apiResource('contact-submissions', ContactSubmissionController::class)->only(['store']);
	Route::post('contact-submissions', [ContactSubmissionController::class, 'store']);
>>>>>>> 52c66d53a61884dec721d8dbc5bd5b51543aec15

	// Unsplash images
	Route::middleware('throttle:60,1')->group(function () {
		Route::get('unsplash/image/general', [BackgroundImageController::class, 'general']);
		Route::get('unsplash/image/seasonal', [BackgroundImageController::class, 'seasonal']);
	});

	// Authenticated and verified routes
	Route::middleware(['auth:sanctum', 'verified'])->group(function () {
		Route::middleware('role:admin')->group(function () {
			// Admin-only routes for managing contact submissions and users
			Route::apiResource('contact-submissions', ContactSubmissionController::class)->except(['store']);
			Route::post('users', [AdminUserController::class, 'store']);
			Route::get('users', [AdminUserController::class, 'index']);
			Route::patch('users/{user}/role', [AdminUserController::class, 'updateRole']);
			Route::delete('users/{user}', [AdminUserController::class, 'destroy']);
		});

		// Admin-only routes for managing categories
		Route::middleware('role:admin')->group(function () {
			Route::apiResource('categories', CategoryController::class);
		});

		// User profile -> self
		Route::get('me', function (Request $request) {
			return response()->json((new UserResource($request->user()))->resolve($request));
		});

		// NextCloud Monitor
		Route::get('monitor/nextcloud', [MonitorController::class, 'status']);

		// Teams management
		Route::apiResource('teams', TeamController::class)->except(['show']);
		// Überschreibt apiResource für das Anzeigen eines Teams nach Slug
		Route::get('teams/{team:slug}', [TeamController::class, 'show']);
		// Invitations for a team
		Route::get('teams/{team}/invitations', [TeamInvitationController::class, 'index']);
		Route::post('teams/{team}/invitations', [TeamInvitationController::class, 'store'])->middleware('throttle:20,1');
		Route::delete('teams/{team}/invitations/{invitation}', [TeamInvitationController::class, 'destroy']);
		Route::post('teams/{team}/transfer-ownership', [TeamMemberController::class, 'transferOwnership']);
		Route::patch('teams/{team}/members/{user}', [TeamMemberController::class, 'update']);
		Route::delete('teams/{team}/members/{user}', [TeamMemberController::class, 'destroy']);
		// Accept invitation for a team invitation
		Route::post('team-invitations/accept', [TeamInvitationController::class, 'accept'])->middleware('throttle:10,1');
		Route::apiResource('workspaces', WorkspaceController::class);
	});
});
