<div class="min-h-screen">
    <x-app-header :back-route="route('dashboard')" />

    <main class="mx-auto max-w-2xl px-6 py-12 lg:py-16">
        {{-- Poll info section --}}
        @if ($editingPollInfo)
            <div class="animate-fade-in">
                <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight font-display">
                    {{ __('Poll bewerken') }}
                </flux:heading>
                <flux:subheading class="mt-2 !text-zinc-500">
                    {{ __('Pas de titel en beschrijving van je poll aan.') }}
                </flux:subheading>
            </div>

            <form wire:submit="savePollInfo" class="mt-8 animate-slide-up vota-card space-y-6 p-8">
                <flux:field>
                    <flux:label>{{ __('Titel') }}</flux:label>
                    <flux:input wire:model="title" autofocus />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Beschrijving') }}</flux:label>
                    <flux:textarea wire:model="description" rows="4" />
                    <flux:error name="description" />
                </flux:field>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <flux:button variant="subtle" wire:click="cancelEditingPollInfo" type="button">
                        {{ __('Annuleren') }}
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        {{ __('Opslaan') }}
                    </flux:button>
                </div>
            </form>
        @else
            <div class="animate-fade-in">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight font-display">
                            {{ $poll->title }}
                        </flux:heading>
                        @if ($poll->description)
                            <flux:subheading class="mt-2 !text-zinc-500">
                                {{ $poll->description }}
                            </flux:subheading>
                        @endif
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <flux:button variant="subtle" size="sm" icon="pencil-square" wire:click="startEditingPollInfo">
                            {{ __('Bewerken') }}
                        </flux:button>

                        <flux:modal.trigger name="delete-poll">
                            <flux:button variant="subtle" size="sm" icon="trash" class="text-red-500 hover:text-red-700">
                                {{ __('Verwijderen') }}
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>

                <div class="mt-3 flex items-center gap-3">
                    @if($poll->isOpen())
                        <flux:badge color="green" size="sm">{{ __('Open') }}</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">{{ __('Gesloten') }}</flux:badge>
                    @endif
                    <span class="text-sm text-zinc-400">{{ $poll->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @endif

        {{-- Options --}}
        <div class="mt-8 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ __('Opties') }} ({{ $this->options->count() }})</flux:heading>

                <a href="{{ route('option.create', $poll) }}" wire:navigate>
                    <flux:button variant="primary" size="sm" icon="plus">
                        {{ __('Optie toevoegen') }}
                    </flux:button>
                </a>
            </div>

            @if ($this->options->isNotEmpty())
                <ul wire:sort="reorderOptions" class="mt-4 space-y-3">
                    @foreach ($this->options as $option)
                        <li wire:key="option-{{ $option->id }}" wire:sort:item="{{ $option->id }}" class="vota-card !rounded-xl">
                            <div class="flex items-center gap-4 p-4">
                                {{-- Drag handle --}}
                                <div wire:sort:handle class="cursor-grab text-zinc-300 hover:text-zinc-500 active:cursor-grabbing">
                                    <flux:icon.bars-3 variant="mini" />
                                </div>

                                @if ($option->image_url)
                                    <img src="{{ $option->image_url }}" alt="{{ $option->name }}" class="h-12 w-12 shrink-0 rounded-lg object-cover" onerror="this.style.display='none'" />
                                @else
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-zinc-100">
                                        <flux:icon.photo variant="mini" class="text-zinc-400" />
                                    </div>
                                @endif

                                <a href="{{ route('option.manage', [$poll, $option]) }}" class="min-w-0 flex-1 group/link" wire:navigate wire:sort:ignore>
                                    <p class="truncate font-medium text-zinc-900 group-hover/link:text-[var(--color-vota-primary)] transition-colors">{{ $option->name }}</p>
                                    @if ($option->description)
                                        <p class="mt-0.5 truncate text-sm text-zinc-500">{{ $option->description }}</p>
                                    @endif
                                </a>

                                {{-- Actions --}}
                                <div wire:sort:ignore class="flex shrink-0 items-center gap-1">
                                    <a href="{{ route('option.manage', [$poll, $option]) }}" wire:navigate>
                                        <flux:button variant="subtle" size="sm" icon="pencil-square" />
                                    </a>
                                    <flux:button variant="subtle" size="sm" icon="trash" wire:click="removeOption({{ $option->id }})" wire:confirm="{{ __('Weet je zeker dat je deze optie wilt verwijderen?') }}" class="text-red-500 hover:text-red-700" />
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="mt-4 vota-card p-8 text-center">
                    <div class="flex h-12 w-12 mx-auto items-center justify-center rounded-2xl" style="background-color: var(--color-vota-primary-light);">
                        <flux:icon.plus class="h-6 w-6" style="color: var(--color-vota-primary);" />
                    </div>
                    <p class="mt-4 text-sm text-zinc-500">{{ __('Deze poll heeft nog geen opties.') }}</p>
                    <a href="{{ route('option.create', $poll) }}" wire:navigate class="mt-4 inline-block">
                        <flux:button variant="primary" size="sm">
                            {{ __('Eerste optie toevoegen') }}
                        </flux:button>
                    </a>
                </div>
            @endif
        </div>
    </main>

    {{-- Delete confirmation modal --}}
    <flux:modal name="delete-poll" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Poll verwijderen?') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Je staat op het punt om deze poll te verwijderen.') }}<br>
                    {{ __('Dit kan niet ongedaan worden gemaakt.') }}
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Annuleren') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="deletePoll">
                    {{ __('Verwijderen') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
