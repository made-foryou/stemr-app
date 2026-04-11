<div class="min-h-screen">
    <x-app-header :back-route="route('dashboard')" />

    {{-- Step indicator --}}
    <div class="mx-auto max-w-2xl px-6 pt-8">
        <nav class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                @if ($step > 1)
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white">
                        <flux:icon.check variant="micro" />
                    </span>
                @else
                    <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold text-white" style="background-color: var(--color-vota-primary);">1</span>
                @endif
                <span class="text-sm font-medium {{ $step === 1 ? 'text-zinc-900' : 'text-zinc-500' }}">{{ __('Poll info') }}</span>
            </div>

            <div class="h-px flex-1 bg-zinc-200"></div>

            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold {{ $step === 2 ? 'text-white' : 'bg-zinc-200 text-zinc-500' }}" @if($step === 2) style="background-color: var(--color-vota-primary);" @endif>2</span>
                <span class="text-sm font-medium {{ $step === 2 ? 'text-zinc-900' : 'text-zinc-500' }}">{{ __('Opties') }}</span>
            </div>
        </nav>
    </div>

    <main class="mx-auto max-w-2xl px-6 py-8 lg:py-12">
        @if ($step === 1)
            {{-- Step 1: Poll info --}}
            <div class="animate-fade-in">
                <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight font-display">
                    {{ __('Nieuwe poll') }}
                </flux:heading>
                <flux:subheading class="mt-2 !text-zinc-500">
                    {{ __('Geef je poll een titel en optioneel een beschrijving.') }}
                </flux:subheading>
            </div>

            <form wire:submit="savePollInfo" class="mt-10 animate-slide-up vota-card space-y-6 p-8" style="animation-delay: 0.15s;">
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
                        {{ __('Volgende') }}
                    </flux:button>
                </div>
            </form>
        @else
            {{-- Step 2: Options --}}
            <div class="animate-fade-in">
                <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight font-display">
                    {{ __('Opties toevoegen') }}
                </flux:heading>
                <flux:subheading class="mt-2 !text-zinc-500">
                    {{ __('Voeg minimaal 2 opties toe waaruit deelnemers kunnen kiezen.') }}
                </flux:subheading>
            </div>

            {{-- Option list --}}
            @if ($this->options->isNotEmpty())
                <ul wire:sort="reorderOptions" class="mt-8 space-y-3">
                    @foreach ($this->options as $option)
                        <li wire:key="option-{{ $option->id }}" wire:sort:item="{{ $option->id }}" class="animate-slide-up vota-card !rounded-xl"
                            <div class="flex items-center gap-4 p-4">
                                {{-- Drag handle --}}
                                <div wire:sort:handle class="cursor-grab text-zinc-300 hover:text-zinc-500 active:cursor-grabbing">
                                    <flux:icon.bars-3 variant="mini" />
                                </div>

                                {{-- Image thumbnail --}}
                                @if ($option->image_url)
                                    <img src="{{ $option->image_url }}" alt="{{ $option->name }}" class="h-12 w-12 shrink-0 rounded-lg object-cover" onerror="this.style.display='none'" />
                                @else
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-zinc-100">
                                        <flux:icon.photo variant="mini" class="text-zinc-400" />
                                    </div>
                                @endif

                                {{-- Content --}}
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-medium text-zinc-900">{{ $option->name }}</p>
                                    @if ($option->description)
                                        <p class="mt-0.5 truncate text-sm text-zinc-500">{{ $option->description }}</p>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div wire:sort:ignore class="flex shrink-0 items-center gap-1">
                                    <flux:button variant="subtle" size="sm" icon="pencil-square" wire:click="editOption({{ $option->id }})" />
                                    <flux:button variant="subtle" size="sm" icon="trash" wire:click="removeOption({{ $option->id }})" wire:confirm="{{ __('Weet je zeker dat je deze optie wilt verwijderen?') }}" class="text-red-500 hover:text-red-700" />
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Add / Edit option form --}}
            <div class="mt-6 animate-slide-up vota-card p-6" style="animation-delay: 0.1s;">
                <flux:heading size="lg">
                    {{ $editingOptionId ? __('Optie bewerken') : __('Optie toevoegen') }}
                </flux:heading>

                <form wire:submit="{{ $editingOptionId ? 'updateOption' : 'addOption' }}" class="mt-4 space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Naam') }} <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="optionName" placeholder="{{ __('Bijv. Restaurant De Kas') }}" maxlength="100" />
                        <flux:error name="optionName" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Afbeelding URL') }}</flux:label>
                        <flux:input wire:model.blur="optionImageUrl" type="url" placeholder="{{ __('https://voorbeeld.nl/afbeelding.jpg') }}" />
                        <flux:error name="optionImageUrl" />
                        @if ($optionImageUrl)
                            <div class="mt-2" x-data="{ error: false }">
                                <img
                                    src="{{ $optionImageUrl }}"
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
                            <span class="text-xs text-zinc-400">{{ strlen($optionDescription) }}/500</span>
                        </div>
                        <flux:textarea wire:model="optionDescription" placeholder="{{ __('Optioneel: beschrijf deze optie...') }}" rows="3" maxlength="500" />
                        <flux:error name="optionDescription" />
                    </flux:field>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        @if ($editingOptionId)
                            <flux:button variant="subtle" wire:click="cancelEdit" type="button">
                                {{ __('Annuleren') }}
                            </flux:button>
                        @endif

                        <flux:button type="submit" variant="primary">
                            {{ $editingOptionId ? __('Opslaan') : __('Toevoegen') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            {{-- Error message --}}
            @error('options')
                <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-600">
                    {{ $message }}
                </div>
            @enderror

            {{-- Footer actions --}}
            <div class="mt-8 flex items-center justify-between">
                <p class="text-sm text-zinc-500">
                    {{ trans_choice(':count optie|:count opties', $this->options->count(), ['count' => $this->options->count()]) }}
                </p>

                <flux:button variant="primary" wire:click="finish" :disabled="$this->options->count() < 2">
                    {{ __('Voltooien') }}
                </flux:button>
            </div>
        @endif
    </main>
</div>
