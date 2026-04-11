@props([
    'backRoute' => null,
    'backLabel' => null,
])

<header class="vota-header">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3" wire:navigate>
            <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background-color: var(--color-vota-primary);">
                <x-app-logo-icon class="h-4 w-4 fill-current text-white" />
            </span>
            <span class="text-lg font-semibold tracking-tight" style="color: var(--color-vota-text);">{{ config('app.name', 'Vota') }}</span>
        </a>

        <div class="flex items-center gap-3">
            {{ $actions ?? '' }}

            @if ($backRoute)
                <a href="{{ $backRoute }}" wire:navigate>
                    <flux:button variant="subtle" size="sm" icon="arrow-left">
                        {{ $backLabel ?? __('Terug') }}
                    </flux:button>
                </a>
            @endif
        </div>
    </div>
</header>
