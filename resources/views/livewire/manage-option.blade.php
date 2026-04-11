<div class="min-h-screen">
    <x-app-header :back-route="route('poll.manage', $option->poll)" />

    <main class="mx-auto max-w-2xl px-6 py-12 lg:py-16">
        <div class="animate-fade-in">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight font-display">
                        {{ $option->name }}
                    </flux:heading>
                    <flux:subheading class="mt-2 !text-zinc-500">
                        {{ __('Onderdeel van') }}
                        <a href="{{ route('poll.manage', $option->poll) }}" class="underline decoration-zinc-300 underline-offset-2 transition-colors hover:decoration-zinc-500" wire:navigate>{{ $option->poll->title }}</a>
                    </flux:subheading>
                </div>

                <flux:modal.trigger name="delete-option">
                    <flux:button variant="subtle" size="sm" icon="trash" class="text-red-500 hover:text-red-700">
                        {{ __('Verwijderen') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        {{-- Image preview --}}
        @if ($option->image_url)
            <div class="mt-8 animate-slide-up overflow-hidden rounded-2xl">
                <img
                    src="{{ $option->image_url }}"
                    alt="{{ $option->name }}"
                    class="h-48 w-full object-cover"
                    onerror="this.parentElement.style.display='none'"
                />
            </div>
        @endif

        {{-- Edit form --}}
        <form wire:submit="save" class="mt-8 animate-slide-up vota-card space-y-6 p-8" style="animation-delay: 0.1s;">
            <flux:heading size="lg">{{ __('Gegevens bewerken') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Naam') }} <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="name" maxlength="100" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Afbeelding URL') }}</flux:label>
                <flux:input wire:model.blur="imageUrl" type="url" placeholder="{{ __('https://voorbeeld.nl/afbeelding.jpg') }}" />
                <flux:error name="imageUrl" />
                @if ($imageUrl)
                    <div class="mt-2" x-data="{ error: false }">
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ __('Preview') }}"
                            class="h-24 w-24 rounded-lg object-cover"
                            x-show="!error"
                            x-on:error="error = true"
                            x-on:load="error = false"
                        />
                        <div x-show="error" class="flex h-24 w-24 items-center justify-center rounded-lg bg-red-50 text-red-400">
                            <flux:icon.exclamation-triangle variant="mini" />
                        </div>
                    </div>
                @endif
            </flux:field>

            <flux:field>
                <div class="flex items-center justify-between">
                    <flux:label>{{ __('Omschrijving') }}</flux:label>
                    <span class="text-xs text-zinc-400">{{ strlen($description) }}/500</span>
                </div>
                <flux:textarea wire:model="description" placeholder="{{ __('Optioneel: beschrijf deze optie...') }}" rows="4" maxlength="500" />
                <flux:error name="description" />
            </flux:field>

            <div class="flex items-center gap-4 pt-2">
                <flux:button type="submit" variant="primary">
                    {{ __('Opslaan') }}
                </flux:button>

                <x-action-message on="option-saved" class="text-sm" style="color: var(--color-vota-primary);">
                    {{ __('Optie opgeslagen.') }}
                </x-action-message>
            </div>
        </form>
    </main>

    {{-- Delete confirmation modal --}}
    <flux:modal name="delete-option" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Optie verwijderen?') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Je staat op het punt om ":name" te verwijderen.', ['name' => $option->name]) }}<br>
                    {{ __('Dit kan niet ongedaan worden gemaakt.') }}
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Annuleren') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="deleteOption">
                    {{ __('Verwijderen') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
