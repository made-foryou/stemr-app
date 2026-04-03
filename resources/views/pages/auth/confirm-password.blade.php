<x-layouts::auth :title="__('Wachtwoord bevestigen')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Wachtwoord bevestigen')"
            :description="__('Dit is een beveiligd gedeelte. Bevestig je wachtwoord om verder te gaan.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('Wachtwoord')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Wachtwoord')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                {{ __('Bevestigen') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
