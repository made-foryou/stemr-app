<?php

use App\Http\Controllers\Auth\AppleController;
use App\Http\Controllers\Auth\GoogleController;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::view('/poll/create', 'pages.poll.create-placeholder')->name('poll.create');
    Route::view('/poll/{poll:slug}/manage', 'pages.poll.manage-placeholder')->name('poll.manage');
});

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

    Route::get('/auth/apple/redirect', [AppleController::class, 'redirect'])->name('auth.apple.redirect');
    Route::post('/auth/apple/callback', [AppleController::class, 'callback'])->name('auth.apple.callback');
});
