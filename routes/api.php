<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HyperlinkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BackgroundController;
use App\Http\Controllers\Api\ContactSubmissionController;

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

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('contact-submissions', ContactSubmissionController::class)->except(['store']);
});

// Public register route
Route::post('/register', [AuthController::class, 'register']);
// Public login route to get a token
Route::post('/login', [AuthController::class, 'login']);

// Public unsplash image endpoints (no auth)
Route::middleware('throttle:60,1')->group(function () {
	Route::get('/unsplash/image/general', [BackgroundController::class, 'background']);
	Route::get('/unsplash/image/seasonal', [BackgroundController::class, 'seasonal']);
});

Route::middleware('auth:sanctum')->group(function () {
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::post('/logout-all', [AuthController::class, 'logoutAll']);
	Route::apiResource('hyperlinks', HyperlinkController::class);
	Route::apiResource('categories', CategoryController::class);
	Route::get('/user', function (Request $request) {
		return $request->user();
	});
});
