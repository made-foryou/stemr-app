<div class="min-h-screen">
    <x-app-header :back-route="route('dashboard')" back-label="{{ __('Dashboard') }}">
        <x-slot:actions>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="subtle" type="submit" size="sm">
                    {{ __('Uitloggen') }}
                </flux:button>
            </form>
        </x-slot:actions>
    </x-app-header>

    <main class="mx-auto max-w-3xl px-6 py-12">
        <div class="animate-fade-in">
            <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight font-display">
                {{ __('Account instellingen') }}
            </flux:heading>
        </div>

        <div class="mt-8 space-y-8">
            {{-- ═══════════════════════════════════════════ --}}
            {{-- SECTIE 1: Wachtwoord instellen / wijzigen  --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="animate-slide-up vota-card p-6 lg:p-8" style="animation-delay: 0.05s;">
                <flux:heading size="lg" class="!font-semibold">{{ __('Wachtwoord') }}</flux:heading>

                @if(! auth()->user()->hasPassword())
                    <flux:subheading class="mt-1 !text-zinc-500">
                        {{ __('Je hebt nog geen wachtwoord ingesteld.') }}
                    </flux:subheading>
                @endif

                <form wire:submit="updatePassword" class="mt-6 space-y-5">
                    @if(auth()->user()->hasPassword())
                        <flux:input
                            wire:model="current_password"
                            name="current_password"
                            :label="__('Huidig wachtwoord')"
                            type="password"
                            required
                            autocomplete="current-password"
                            :placeholder="__('Huidig wachtwoord')"
                            viewable
                        />
                    @endif

                    <flux:input
                        wire:model="password"
                        name="password"
                        :label="__('Nieuw wachtwoord')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Minimaal 8 tekens')"
                        viewable
                    />

                    <flux:input
                        wire:model="password_confirmation"
                        name="password_confirmation"
                        :label="__('Wachtwoord bevestigen')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Herhaal je wachtwoord')"
                        viewable
                    />

                    <div class="flex items-center gap-4">
                        <flux:button variant="primary" type="submit">
                            {{ __('Wachtwoord opslaan') }}
                        </flux:button>

                        <x-action-message on="password-updated" class="text-sm" style="color: var(--color-vota-primary);">
                            {{ __('Wachtwoord is bijgewerkt.') }}
                        </x-action-message>
                    </div>
                </form>
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- SECTIE 2: Google                            --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="animate-slide-up vota-card p-6 lg:p-8" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                        </div>
                        <div>
                            @if(auth()->user()->hasGoogleLinked())
                                <flux:heading class="!font-semibold">{{ __('Google gekoppeld') }}</flux:heading>
                            @else
                                <flux:heading class="!font-semibold">Google</flux:heading>
                                <flux:subheading class="!text-sm !text-zinc-400">{{ __('Niet gekoppeld') }}</flux:subheading>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if(auth()->user()->hasGoogleLinked())
                            <x-action-message on="provider-disconnected" class="text-sm" style="color: var(--color-vota-primary);">
                                {{ __('Je Google-account is losgekoppeld.') }}
                            </x-action-message>

                            @if(auth()->user()->canDisconnectProvider('google'))
                                <flux:button
                                    variant="danger"
                                    size="sm"
                                    wire:click="disconnectGoogle"
                                    wire:confirm="{{ __('Weet je het zeker? Je kunt daarna niet meer inloggen met Google.') }}"
                                >
                                    {{ __('Loskoppelen') }}
                                </flux:button>
                            @else
                                <flux:tooltip content="{{ __('Stel eerst een wachtwoord in of koppel een andere provider voordat je deze loskoppelt.') }}">
                                    <flux:button variant="danger" size="sm" disabled>
                                        {{ __('Loskoppelen') }}
                                    </flux:button>
                                </flux:tooltip>
                            @endif
                        @else
                            <a href="{{ route('auth.google.redirect') }}">
                                <flux:button variant="primary" size="sm">
                                    {{ __('Google koppelen') }}
                                </flux:button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- SECTIE 3: Apple                             --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="animate-slide-up vota-card p-6 lg:p-8" style="animation-delay: 0.15s;">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" style="color: var(--color-vota-text);">
                                <path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                            </svg>
                        </div>
                        <div>
                            @if(auth()->user()->hasAppleLinked())
                                <flux:heading class="!font-semibold">{{ __('Apple gekoppeld') }}</flux:heading>
                            @else
                                <flux:heading class="!font-semibold">Apple</flux:heading>
                                <flux:subheading class="!text-sm !text-zinc-400">{{ __('Niet gekoppeld') }}</flux:subheading>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if(auth()->user()->hasAppleLinked())
                            <x-action-message on="provider-disconnected" class="text-sm" style="color: var(--color-vota-primary);">
                                {{ __('Je Apple-account is losgekoppeld.') }}
                            </x-action-message>

                            @if(auth()->user()->canDisconnectProvider('apple'))
                                <flux:button
                                    variant="danger"
                                    size="sm"
                                    wire:click="disconnectApple"
                                    wire:confirm="{{ __('Weet je het zeker? Je kunt daarna niet meer inloggen met Apple.') }}"
                                >
                                    {{ __('Loskoppelen') }}
                                </flux:button>
                            @else
                                <flux:tooltip content="{{ __('Stel eerst een wachtwoord in of koppel een andere provider voordat je deze loskoppelt.') }}">
                                    <flux:button variant="danger" size="sm" disabled>
                                        {{ __('Loskoppelen') }}
                                    </flux:button>
                                </flux:tooltip>
                            @endif
                        @else
                            <a href="{{ route('auth.apple.redirect') }}">
                                <flux:button variant="primary" size="sm">
                                    {{ __('Apple koppelen') }}
                                </flux:button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
