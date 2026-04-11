<?php

use App\Http\Controllers\Auth\AppleController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
use App\Livewire\AccountSettings;
use App\Livewire\CreateOption;
use App\Livewire\CreatePoll;
use App\Livewire\Dashboard;
use App\Livewire\ManageOption;
use App\Livewire\ManagePoll;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/account', AccountSettings::class)->name('account.settings');
    Route::get('/poll/create', CreatePoll::class)->name('poll.create');
    Route::get('/poll/{poll:slug}/manage', ManagePoll::class)->name('poll.manage');
    Route::get('/poll/{poll:slug}/option/create', CreateOption::class)->name('option.create');
    Route::get('/poll/{poll:slug}/option/{option}', ManageOption::class)->name('option.manage');
});

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::get('/auth/apple/redirect', [AppleController::class, 'redirect'])->name('auth.apple.redirect');
Route::post('/auth/apple/callback', [AppleController::class, 'callback'])->name('auth.apple.callback');
