@props(['model', 'isFetchingUrl', 'isScrapeSlow' => false, 'fetchError' => ''])

<flux:field>
    <flux:label>{{ __('URL importeren') }}</flux:label>
    <div class="flex gap-2">
        <div class="relative flex-1">
            <flux:input wire:model.live.blur="{{ $model }}" type="url" placeholder="{{ __('https://booking.com/hotel/...') }}" {{ $attributes }} />
            @if ($isFetchingUrl)
                <div wire:poll.2s="checkScrapeResult" class="absolute top-1/2 right-3 -translate-y-1/2">
                    <flux:icon.arrow-path variant="mini" class="animate-spin text-zinc-400" />
                </div>
            @endif
        </div>
        <flux:button type="button" variant="subtle" size="sm" icon="arrow-path" wire:click="fetchFromUrl"
            class="shrink-0 self-start !h-[42px]"
            :disabled="$isFetchingUrl" />
    </div>
    <flux:description>{{ __('Plak een URL om automatisch de gegevens op te halen.') }}</flux:description>
    <flux:error :name="$model" />

    @if ($isFetchingUrl && $isScrapeSlow)
        <div class="flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 animate-fade-in">
            <flux:icon.clock variant="mini" class="mt-0.5 shrink-0 text-amber-500" />
            <span>{{ __('De URL wordt op de achtergrond opgehaald. Sommige websites hebben extra beveiligingsmaatregelen, waardoor dit wat langer kan duren. Je kunt ondertussen verder met invullen.') }}</span>
        </div>
    @endif

    @if ($fetchError)
        <p class="mt-1 text-sm text-red-600">{{ $fetchError }}</p>
    @endif
</flux:field>
