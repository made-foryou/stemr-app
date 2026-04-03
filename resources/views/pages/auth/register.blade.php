<x-layouts::auth :title="__('Registreren')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Account aanmaken')" :description="__('Vul je gegevens in om een account aan te maken')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-social-login-buttons />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Naam')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Volledige naam')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('E-mailadres')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="naam@voorbeeld.nl"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Wachtwoord')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Minimaal 8 tekens')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Wachtwoord bevestigen')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Herhaal je wachtwoord')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Account aanmaken') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-500">
            <span>{{ __('Heb je al een account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Inloggen') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
