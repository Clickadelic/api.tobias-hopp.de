<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BackgroundImageController;
use App\Http\Controllers\Api\ContactSubmissionController;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\AdminUserController;

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


// Public
Route::apiResource('contact-submissions', ContactSubmissionController::class)->only(['store']);

// Public register route
Route::post('/register', [AuthController::class, 'register']);
// Public login route to get a token
Route::post('/login', [AuthController::class, 'login']);
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
	->middleware(['auth:sanctum', 'throttle:6,1']);

// Public unsplash image endpoints (no auth)
Route::middleware('throttle:60,1')->group(function () {
	Route::get('/unsplash/image/general', [BackgroundImageController::class, 'background']);
	Route::get('/unsplash/image/seasonal', [BackgroundImageController::class, 'seasonal']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
	Route::middleware('role:admin')->group(function () {
		Route::apiResource('contact-submissions', ContactSubmissionController::class)->except(['store']);
	});

	// Auth routes
	Route::post('logout', [AuthController::class, 'logout']);
	Route::post('logout-all', [AuthController::class, 'logoutAll']);
	Route::get('user', function (Request $request) {
		return response()->json((new \App\Http\Resources\UserResource($request->user()))->resolve($request));
	});
	// Monitor route
	Route::get('monitor/nextcloud', [MonitorController::class, 'status']);

	Route::middleware('role:admin')->prefix('admin')->group(function () {
		Route::get('users', [AdminUserController::class, 'index']);
		Route::patch('users/{user}/role', [AdminUserController::class, 'updateRole']);
	});
});
