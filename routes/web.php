<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSubmissionMail;

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/docs', [PageController::class, 'docs'])->name('docs');

if (config('app.env') === 'local') {
	include_once __DIR__ . '/dev.php';
}
