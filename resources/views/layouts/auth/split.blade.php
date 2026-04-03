<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased" style="background-color: var(--color-vota-bg); color: var(--color-vota-text);">
        <div class="relative grid min-h-dvh lg:grid-cols-[1fr_1.1fr]">
            {{-- Branding panel --}}
            <div class="relative hidden flex-col justify-between overflow-hidden p-12 lg:flex" style="background-color: var(--color-vota-primary-dark);">
                {{-- Subtle geometric pattern --}}
                <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
                <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full blur-3xl" style="background-color: rgba(107, 53, 104, 0.15);"></div>
                <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full blur-3xl" style="background-color: rgba(196, 112, 63, 0.1);"></div>

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="relative z-10 flex items-center gap-3" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background-color: var(--color-vota-primary);">
                        <x-app-logo-icon class="h-5 w-5 fill-current text-white" />
                    </span>
                    <span class="text-xl font-semibold tracking-tight text-white">{{ config('app.name', 'Vota') }}</span>
                </a>

                {{-- Animated graphic --}}
                <div class="relative z-10 mx-auto my-auto aspect-square w-full max-w-xs">
                    {{-- Ambient glow --}}
                    <div class="absolute left-1/2 top-1/2 h-48 w-48 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl vota-pulse" style="background-color: rgba(107, 53, 104, 0.12);"></div>

                    {{-- Orbital rings --}}
                    <div class="absolute inset-6 rounded-full border border-white/[0.08]"></div>
                    <div class="absolute inset-16 rounded-full border border-white/[0.05]"></div>

                    {{-- Center piece — checkmark --}}
                    <div class="absolute left-1/2 top-1/2 flex h-16 w-16 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full shadow-lg vota-breathe" style="background-color: var(--color-vota-primary); box-shadow: 0 10px 25px rgba(107, 53, 104, 0.3);">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>

                    {{-- Orbiting dots --}}
                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 25s;">
                        <div class="absolute left-1/2 top-0 h-3.5 w-3.5 -translate-x-1/2 rounded-full shadow-md vota-breathe" style="background-color: var(--color-vota-primary); box-shadow: 0 4px 12px rgba(107, 53, 104, 0.3); animation-delay: 0.5s;"></div>
                    </div>

                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 30s; animation-direction: reverse;">
                        <div class="absolute left-1/2 top-0 h-2.5 w-2.5 -translate-x-1/2 rounded-full shadow-md vota-breathe" style="background-color: var(--color-vota-accent); box-shadow: 0 4px 12px rgba(196, 112, 63, 0.3); animation-delay: 1.2s;"></div>
                    </div>

                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 20s;">
                        <div class="absolute left-0 top-1/2 h-3 w-3 -translate-y-1/2 rounded-full shadow-md vota-breathe" style="background-color: #8B4E88; box-shadow: 0 4px 12px rgba(139, 78, 136, 0.3); animation-delay: 0.8s;"></div>
                    </div>

                    {{-- Floating geometric accents --}}
                    <div class="absolute right-4 top-8 vota-float" style="animation-delay: 0.3s;">
                        <div class="h-6 w-6 rotate-45 rounded-lg border-2" style="border-color: rgba(107, 53, 104, 0.25); background-color: rgba(107, 53, 104, 0.08);"></div>
                    </div>

                    <div class="absolute bottom-12 left-6 vota-float" style="animation-delay: 1.5s;">
                        <div class="h-5 w-5 rounded-full border-2" style="border-color: rgba(196, 112, 63, 0.2); background-color: rgba(196, 112, 63, 0.06);"></div>
                    </div>

                    <div class="absolute bottom-24 right-10 vota-float" style="animation-delay: 2.2s;">
                        <div class="h-4 w-4 rotate-12 rounded-md border-2" style="border-color: rgba(139, 78, 136, 0.18); background-color: rgba(139, 78, 136, 0.05);"></div>
                    </div>
                </div>

                {{-- Tagline --}}
                <div class="relative z-10 space-y-6">
                    <h2 class="text-4xl font-bold leading-tight tracking-tight text-white">
                        Samen de beste<br>keuze maken.
                    </h2>
                    <p class="max-w-sm text-lg font-light leading-relaxed text-white/60">
                        Maak een poll, nodig je team uit, en ontdek welke optie de favoriet is.
                    </p>
                </div>
            </div>

            {{-- Form panel --}}
            <div class="flex items-center justify-center px-6 py-12 lg:px-16">
                <div class="w-full max-w-sm animate-fade-in">
                    {{-- Mobile logo --}}
                    <a href="{{ route('home') }}" class="mb-10 flex flex-col items-center gap-3 lg:hidden" wire:navigate>
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl" style="background-color: var(--color-vota-primary);">
                            <x-app-logo-icon class="h-6 w-6 fill-current text-white" />
                        </span>
                        <span class="text-lg font-semibold tracking-tight" style="color: var(--color-vota-text);">{{ config('app.name', 'Vota') }}</span>
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
