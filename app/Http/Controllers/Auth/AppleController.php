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
            return redirect()->route('login')->with(
                'status',
                __('Er is een fout opgetreden bij het inloggen met Apple. Probeer het opnieuw.'),
            );
        }

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
