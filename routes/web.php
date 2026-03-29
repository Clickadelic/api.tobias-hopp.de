<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class,'index'])->name('home');
Route::get('/about', [PageController::class,'about'])->name('about');
