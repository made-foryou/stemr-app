<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Account instellingen')]
class AccountSettings extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        $user = auth()->user();

        $rules = [
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ];

        if ($user->hasPassword()) {
            $rules['current_password'] = ['required', 'string', 'current_password'];
        }

        $this->validate($rules);

        $user->forceFill([
            'password' => Hash::make($this->password),
        ])->save();

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }

    public function disconnectGoogle(): void
    {
        $user = auth()->user();

        if (! $user->canDisconnectProvider('google')) {
            throw ValidationException::withMessages([
                'google' => __('Stel eerst een wachtwoord in of koppel een andere provider voordat je deze loskoppelt.'),
            ]);
        }

        $user->forceFill(['google_id' => null])->save();

        $this->dispatch('provider-disconnected');
    }

    public function disconnectApple(): void
    {
        $user = auth()->user();

        if (! $user->canDisconnectProvider('apple')) {
            throw ValidationException::withMessages([
                'apple' => __('Stel eerst een wachtwoord in of koppel een andere provider voordat je deze loskoppelt.'),
            ]);
        }

        $user->forceFill(['apple_id' => null])->save();

        $this->dispatch('provider-disconnected');
    }

    public function render(): View
    {
        return view('livewire.account-settings');
    }
}
