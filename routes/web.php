<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Api\EmailVerificationController;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSubmissionMail;

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/docs', [PageController::class, 'docs'])->name('docs');

Route::get('/disclaimer', [PageController::class, 'disclaimer'])->name('disclaimer');
Route::get('/cookie-policy', [PageController::class, 'cookiepolicy'])->name('cookie-policy');

Route::get('/terms-of-service', [PageController::class, 'termsofservice'])->name('terms-of-service');
Route::get('/terms-of-use', [PageController::class, 'termsofuse'])->name('terms-of-use');
Route::get('/terms-of-privacy', [PageController::class, 'termsofprivacy'])->name('terms-of-privacy');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
	->middleware(['signed', 'throttle:6,1'])
	->name('verification.verify');

// if (config('app.env') === 'local') {
include_once __DIR__ . '/dev.php';
// }
