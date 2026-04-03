<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;

class AppleController extends Controller
{
    /**
     * Redirect the user to Apple's OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('apple')->redirect();
    }

    /**
     * Handle the callback from Apple.
     */
    public function callback(): RedirectResponse
    {
        try {
            $appleUser = Socialite::driver('apple')->user();
        } catch (\Throwable) {
            $route = Auth::check() ? 'account.settings' : 'login';

            return redirect()->route($route)->with(
                'status',
                __('Er is een fout opgetreden bij het inloggen met Apple. Probeer het opnieuw.'),
            );
        }

        // Authenticated user linking their Apple account
        if (Auth::check()) {
            return $this->linkToCurrentUser($appleUser);
        }

        // Guest login/registration flow
        return $this->loginOrRegister($appleUser);
    }

    /**
     * Link Apple account to the currently authenticated user.
     */
    protected function linkToCurrentUser(mixed $appleUser): RedirectResponse
    {
        $existingUser = User::where('apple_id', $appleUser->getId())->first();

        if ($existingUser && $existingUser->id !== Auth::id()) {
            return redirect()->route('account.settings')->with(
                'status',
                __('Dit Apple-account is al gekoppeld aan een ander account.'),
            );
        }

        Auth::user()->forceFill(['apple_id' => $appleUser->getId()])->save();

        return redirect()->route('account.settings');
    }

    /**
     * Login existing user or register new user via Apple.
     */
    protected function loginOrRegister(mixed $appleUser): RedirectResponse
    {
        $user = User::where('apple_id', $appleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $appleUser->getEmail())->first();

            if ($user) {
                $user->update(['apple_id' => $appleUser->getId()]);
            } else {
                $user = User::create([
                    'name' => $appleUser->getName() ?? 'Apple User',
                    'email' => $appleUser->getEmail(),
                    'apple_id' => $appleUser->getId(),
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
