<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            $route = Auth::check() ? 'account.settings' : 'login';

            return redirect()->route($route)->with(
                'status',
                __('Er is een fout opgetreden bij het inloggen met Google. Probeer het opnieuw.'),
            );
        }

        // Authenticated user linking their Google account
        if (Auth::check()) {
            return $this->linkToCurrentUser($googleUser);
        }

        // Guest login/registration flow
        return $this->loginOrRegister($googleUser);
    }

    /**
     * Link Google account to the currently authenticated user.
     */
    protected function linkToCurrentUser(mixed $googleUser): RedirectResponse
    {
        $existingUser = User::where('google_id', $googleUser->getId())->first();

        if ($existingUser && $existingUser->id !== Auth::id()) {
            return redirect()->route('account.settings')->with(
                'status',
                __('Dit Google-account is al gekoppeld aan een ander account.'),
            );
        }

        Auth::user()->forceFill(['google_id' => $googleUser->getId()])->save();

        return redirect()->route('account.settings');
    }

    /**
     * Login existing user or register new user via Google.
     */
    protected function loginOrRegister(mixed $googleUser): RedirectResponse
    {
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]);
            }
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user, remember: true);

        return redirect()->route('dashboard');
    }
}
