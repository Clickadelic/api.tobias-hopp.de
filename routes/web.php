<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class,'index'])->name('home');
Route::get('/login', [PageController::class,'login'])->name('login');
Route::get('/logout', [PageController::class,'logout'])->name('logout');
Route::get('/register', [PageController::class,'register'])->name('register');
Route::get('/forgot-password', [PageController::class,'forgotPassword'])->name('forgot-password');
Route::get('/about', [PageController::class,'about'])->name('about');
