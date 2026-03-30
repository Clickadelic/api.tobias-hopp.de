<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\HyperlinkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BackgroundController;

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

// Public background endpoints (no auth)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/background', [BackgroundController::class, 'background']);
    Route::get('/background/general', [BackgroundController::class, 'background']); // alias for clarity
    Route::get('/background/seasonal', [BackgroundController::class, 'seasonal']);
});

// Public register route
Route::post('/register', [AuthController::class, 'register']);

// Public login route to obtain a token
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::post('/logout-all', [AuthController::class, 'logoutAll']);
	Route::get('/images', [ImageController::class, 'index']);
	Route::apiResource('hyperlinks', HyperlinkController::class);
	Route::apiResource('categories', CategoryController::class);
	Route::get('/user', function (Request $request) {
		return $request->user();
	});
});
