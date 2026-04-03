<x-layouts::app :title="__('Dashboard')">
    <div class="min-h-screen">
        {{-- Top bar --}}
        <header class="border-b border-zinc-200">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background-color: var(--color-vota-primary);">
                        <x-app-logo-icon class="h-4 w-4 fill-current text-white" />
                    </span>
                    <span class="text-lg font-semibold tracking-tight" style="color: var(--color-vota-text);">{{ config('app.name', 'Vota') }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button variant="subtle" type="submit" size="sm">
                        {{ __('Uitloggen') }}
                    </flux:button>
                </form>
            </div>
        </header>

        {{-- Main content — two column on desktop --}}
        <main class="mx-auto max-w-6xl px-6 py-12 lg:py-20">
            <div class="grid items-center gap-12 lg:grid-cols-[1fr_1.1fr] lg:gap-20">

                {{-- Left: animated graphic --}}
                <div class="relative mx-auto aspect-square w-full max-w-md animate-fade-in">
                    {{-- Ambient glow --}}
                    <div class="absolute left-1/2 top-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl vota-pulse" style="background-color: rgba(107, 53, 104, 0.08);"></div>

                    {{-- Orbital ring --}}
                    <div class="absolute inset-8 rounded-full border border-zinc-300/30"></div>
                    <div class="absolute inset-20 rounded-full border border-zinc-300/15"></div>

                    {{-- Center piece — checkmark in circle --}}
                    <div class="absolute left-1/2 top-1/2 flex h-20 w-20 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full shadow-lg vota-breathe" style="background-color: var(--color-vota-primary); box-shadow: 0 10px 25px rgba(107, 53, 104, 0.2);">
                        <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>

                    {{-- Orbiting dots — represent votes/choices --}}
                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 25s;">
                        <div class="absolute left-1/2 top-0 h-4 w-4 -translate-x-1/2 rounded-full shadow-md vota-breathe" style="background-color: var(--color-vota-primary); box-shadow: 0 4px 12px rgba(107, 53, 104, 0.25); animation-delay: 0.5s;"></div>
                    </div>

                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 30s; animation-direction: reverse;">
                        <div class="absolute left-1/2 top-0 h-3 w-3 -translate-x-1/2 rounded-full shadow-md vota-breathe" style="background-color: var(--color-vota-accent); box-shadow: 0 4px 12px rgba(196, 112, 63, 0.25); animation-delay: 1.2s;"></div>
                    </div>

                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 20s;">
                        <div class="absolute left-0 top-1/2 h-3.5 w-3.5 -translate-y-1/2 rounded-full shadow-md vota-breathe" style="background-color: #8B4E88; box-shadow: 0 4px 12px rgba(139, 78, 136, 0.25); animation-delay: 0.8s;"></div>
                    </div>

                    {{-- Floating geometric accents --}}
                    <div class="absolute right-8 top-12 vota-float" style="animation-delay: 0.3s;">
                        <div class="h-8 w-8 rotate-45 rounded-lg border-2" style="border-color: rgba(107, 53, 104, 0.2); background-color: rgba(107, 53, 104, 0.05);"></div>
                    </div>

                    <div class="absolute bottom-16 left-10 vota-float" style="animation-delay: 1.5s;">
                        <div class="h-6 w-6 rounded-full border-2" style="border-color: rgba(196, 112, 63, 0.2); background-color: rgba(196, 112, 63, 0.05);"></div>
                    </div>

                    <div class="absolute bottom-28 right-14 vota-float" style="animation-delay: 2.2s;">
                        <div class="h-5 w-5 rotate-12 rounded-md border-2" style="border-color: rgba(139, 78, 136, 0.15); background-color: rgba(139, 78, 136, 0.04);"></div>
                    </div>

                    <div class="absolute left-20 top-20 vota-float" style="animation-delay: 0.7s;">
                        <div class="h-3 w-3 rounded-full" style="background-color: rgba(107, 53, 104, 0.15);"></div>
                    </div>

                    {{-- Subtle connecting lines --}}
                    <svg class="absolute inset-0 h-full w-full opacity-[0.06]" viewBox="0 0 400 400">
                        <line x1="200" y1="120" x2="300" y2="80" stroke="#6B3568" stroke-width="1" />
                        <line x1="200" y1="120" x2="100" y2="180" stroke="#6B3568" stroke-width="1" />
                        <line x1="200" y1="120" x2="280" y2="300" stroke="#6B3568" stroke-width="1" />
                    </svg>
                </div>

                {{-- Right: content --}}
                <div>
                    <div class="animate-fade-in">
                        <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight lg:!text-4xl">
                            {{ __('Welkom, :name', ['name' => auth()->user()->name]) }}
                        </flux:heading>
                        <flux:subheading class="mt-3 !text-lg !text-zinc-500">
                            {{ __('Je bent klaar om je eerste poll aan te maken.') }}
                        </flux:subheading>
                    </div>

                    <div class="mt-10 animate-slide-up rounded-2xl border border-zinc-200 p-8" style="background-color: white; animation-delay: 0.15s;">
                        <div class="flex flex-col items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl" style="background-color: var(--color-vota-primary-light);">
                                <flux:icon.plus class="h-6 w-6" style="color: var(--color-vota-primary);" />
                            </div>
                            <div>
                                <flux:heading size="lg" class="!font-semibold">{{ __('Nieuwe poll') }}</flux:heading>
                                <flux:subheading class="mt-1 !text-zinc-500">
                                    {{ __('Maak opties aan, nodig je team uit, en ontdek de favoriet.') }}
                                </flux:subheading>
                            </div>
                            <flux:button variant="primary" class="mt-2" disabled>
                                {{ __('Poll aanmaken') }}
                            </flux:button>
                            <flux:text class="!text-xs !text-zinc-500">{{ __('Binnenkort beschikbaar') }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-layouts::app>
