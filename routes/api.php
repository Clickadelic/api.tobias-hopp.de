<?php

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BackgroundImageController;
use App\Http\Controllers\Api\ContactSubmissionController;
use App\Http\Controllers\Api\MonitorController;
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
	Route::post('email/verification-notification', [AuthController::class, 'resendVerification'])
		->middleware(['auth:sanctum', 'throttle:6,1']);

	Route::middleware(['auth:sanctum', 'verified'])->group(function () {
		Route::post('logout', [AuthController::class, 'logout']);
		Route::post('logout-all', [AuthController::class, 'logoutAll']);
	});
});

Route::prefix('v1')->group(function () {
	Route::apiResource('contact-submissions', ContactSubmissionController::class)->only(['store']);

	Route::middleware('throttle:60,1')->group(function () {
		Route::get('unsplash/image/general', [BackgroundImageController::class, 'background']);
		Route::get('unsplash/image/seasonal', [BackgroundImageController::class, 'seasonal']);
	});

	Route::middleware(['auth:sanctum', 'verified'])->group(function () {
		Route::middleware('role:admin')->group(function () {
			Route::apiResource('contact-submissions', ContactSubmissionController::class)->except(['store']);
			Route::get('users', [AdminUserController::class, 'index']);
			Route::patch('users/{user}/role', [AdminUserController::class, 'updateRole']);
		});

		Route::get('me', function (Request $request) {
			return response()->json((new UserResource($request->user()))->resolve($request));
		});
		Route::get('monitor/nextcloud', [MonitorController::class, 'status']);
	});
});
