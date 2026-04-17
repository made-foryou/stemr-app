<div class="min-h-screen">
    <x-app-header :back-route="route('poll.manage', $poll)" />

    <main class="mx-auto max-w-2xl px-6 py-12 lg:py-16">
        <div class="animate-fade-in">
            <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight font-display">
                {{ __('Optie toevoegen') }}
            </flux:heading>
            <flux:subheading class="mt-2 !text-zinc-500">
                {{ __('Voeg een nieuwe optie toe aan') }}
                <a href="{{ route('poll.manage', $poll) }}" class="underline decoration-zinc-300 underline-offset-2 transition-colors hover:decoration-zinc-500" wire:navigate>{{ $poll->title }}</a>
            </flux:subheading>
        </div>

        <form wire:submit="save" class="mt-8 animate-slide-up vota-card space-y-6 p-8" style="animation-delay: 0.1s;">
            <flux:field>
                <flux:label>{{ __('URL importeren') }}</flux:label>
                <div class="relative">
                    <flux:input wire:model.live.blur="sourceUrl" type="url" placeholder="{{ __('https://booking.com/hotel/...') }}" autofocus />
                    @if ($isFetchingUrl)
                        <div wire:poll.2s="checkScrapeResult" class="absolute top-1/2 right-3 -translate-y-1/2">
                            <flux:icon.arrow-path variant="mini" class="animate-spin text-zinc-400" />
                        </div>
                    @endif
                </div>
                <flux:description>{{ __('Plak een URL om automatisch de gegevens op te halen.') }}</flux:description>
                <flux:error name="sourceUrl" />
                @if ($fetchError)
                    <p class="mt-1 text-sm text-red-600">{{ $fetchError }}</p>
                @endif
            </flux:field>

            @if (!empty($scrapedData) && !$isFetchingUrl)
                @php
                    $hasSuggestions = collect($scrapedData)->filter(fn($val, $key) =>
                        !empty($val) && isset($this->getScrapedFieldMapping()[$key]) && $this->{$this->getScrapedFieldMapping()[$key]} !== $val
                    )->isNotEmpty();
                @endphp

                @if ($hasSuggestions)
                    <div class="flex items-center justify-between rounded-lg border border-dashed border-[var(--color-vota-primary)]/30 bg-[var(--color-vota-primary-light)] px-4 py-3 animate-fade-in">
                        <div class="flex items-center gap-2 text-sm text-zinc-700">
                            <flux:icon.sparkles variant="mini" class="text-[var(--color-vota-primary)]" />
                            {{ __('Gegevens opgehaald van de URL') }}
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" wire:click="applyAllSuggestions"
                                class="text-sm font-medium text-[var(--color-vota-primary)] transition-colors hover:text-[var(--color-vota-primary-dark)]">
                                {{ __('Alles toepassen') }}
                            </button>
                            <button type="button" wire:click="dismissAllSuggestions"
                                class="text-sm text-zinc-400 transition-colors hover:text-zinc-600">
                                {{ __('Negeren') }}
                            </button>
                        </div>
                    </div>
                @endif
            @endif

            <flux:field>
                <flux:label>{{ __('Naam') }} <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="name" placeholder="{{ __('Bijv. Restaurant De Kas') }}" maxlength="100" />
                <flux:error name="name" />
                @if (!empty($scrapedData['title']) && ($scrapedData['title'] !== $name))
                    <x-scrape-suggestion :value="$scrapedData['title']" field="title" />
                @endif
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Afbeelding URL') }}</flux:label>
                <flux:input wire:model.blur="imageUrl" type="url" placeholder="{{ __('https://voorbeeld.nl/afbeelding.jpg') }}" />
                <flux:error name="imageUrl" />
                @if (!empty($scrapedData['image_url']) && ($scrapedData['image_url'] !== $imageUrl))
                    <x-scrape-suggestion :value="$scrapedData['image_url']" field="image_url" />
                @endif
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
                @if (!empty($scrapedData['description']) && ($scrapedData['description'] !== $description))
                    <x-scrape-suggestion :value="$scrapedData['description']" field="description" />
                @endif
            </flux:field>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('poll.manage', $poll) }}" wire:navigate>
                    <flux:button variant="subtle" type="button">
                        {{ __('Annuleren') }}
                    </flux:button>
                </a>

                <flux:button type="submit" variant="primary">
                    {{ __('Toevoegen') }}
                </flux:button>
            </div>
        </form>
    </main>
</div>
