<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash; // password verify
use Illuminate\Support\Facades\Auth; // attempt

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HyperlinkController;
use App\Http\Controllers\Api\CategoryController;
use App\Models\Category;
use App\Models\User;

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
// Public register route
Route::post('/register', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => $data['password'], // 'hashed' cast on User will hash this
    ]);

    return response()->json(['message' => 'Registered'], 201);
});

// Public login route to obtain a token
Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $data['email'])->first();

    if (! $user || ! Hash::check($data['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('hyperlinks', HyperlinkController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
