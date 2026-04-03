<?php

use App\Http\Controllers\Auth\AppleController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/dashboard', 'pages.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

    Route::get('/auth/apple/redirect', [AppleController::class, 'redirect'])->name('auth.apple.redirect');
    Route::post('/auth/apple/callback', [AppleController::class, 'callback'])->name('auth.apple.callback');
});
