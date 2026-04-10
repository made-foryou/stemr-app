<div class="min-h-screen">
    {{-- Top bar --}}
    <header class="border-b border-zinc-200">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3" wire:navigate>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background-color: var(--color-vota-primary);">
                    <x-app-logo-icon class="h-4 w-4 fill-current text-white" />
                </span>
                <span class="text-lg font-semibold tracking-tight" style="color: var(--color-vota-text);">{{ config('app.name', 'Vota') }}</span>
            </a>

            <a href="{{ route('dashboard') }}" wire:navigate>
                <flux:button variant="subtle" size="sm" icon="arrow-left">
                    {{ __('Terug') }}
                </flux:button>
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-2xl px-6 py-12 lg:py-16">
        <div class="animate-fade-in">
            <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight">
                {{ __('Nieuwe poll') }}
            </flux:heading>
            <flux:subheading class="mt-2 !text-zinc-500">
                {{ __('Geef je poll een titel en optioneel een beschrijving.') }}
            </flux:subheading>
        </div>

        <form wire:submit="save" class="mt-10 animate-slide-up space-y-6 rounded-2xl border border-zinc-200 bg-white p-8" style="animation-delay: 0.15s;">
            <flux:field>
                <flux:label>{{ __('Titel') }}</flux:label>
                <flux:input wire:model="title" placeholder="{{ __('Bijv. Beste restaurant voor teamuitje') }}" autofocus />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Beschrijving') }}</flux:label>
                <flux:textarea wire:model="description" placeholder="{{ __('Optioneel: leg uit waar deze poll over gaat...') }}" rows="4" />
                <flux:error name="description" />
            </flux:field>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <flux:button variant="subtle">
                        {{ __('Annuleren') }}
                    </flux:button>
                </a>

                <flux:button type="submit" variant="primary">
                    {{ __('Poll aanmaken') }}
                </flux:button>
            </div>
        </form>
    </main>
</div>
