<x-layouts::auth :title="__('E-mail verifiëren')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Verifieer je e-mailadres')" :description="__('Klik op de link in de e-mail die we je zojuist hebben gestuurd.')" />

        @if (session('status') == 'verification-link-sent')
            <flux:text class="text-center font-medium !text-emerald-600 dark:!text-emerald-400">
                {{ __('Er is een nieuwe verificatielink verstuurd naar het e-mailadres dat je hebt opgegeven.') }}
            </flux:text>
        @endif

        <div class="flex flex-col items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Verificatiemail opnieuw versturen') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {{ __('Uitloggen') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
