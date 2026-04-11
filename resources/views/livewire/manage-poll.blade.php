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
                {{ $poll->title }}
            </flux:heading>
            @if ($poll->description)
                <flux:subheading class="mt-2 !text-zinc-500">
                    {{ $poll->description }}
                </flux:subheading>
            @endif

            <div class="mt-3 flex items-center gap-3">
                @if($poll->isOpen())
                    <flux:badge color="green" size="sm">{{ __('Open') }}</flux:badge>
                @else
                    <flux:badge color="zinc" size="sm">{{ __('Gesloten') }}</flux:badge>
                @endif
                <span class="text-sm text-zinc-400">{{ $poll->created_at->diffForHumans() }}</span>
            </div>
        </div>

        {{-- Options --}}
        @if ($options->isNotEmpty())
            <div class="mt-8 animate-slide-up space-y-3" style="animation-delay: 0.1s;">
                <flux:heading size="lg">{{ __('Opties') }} ({{ $options->count() }})</flux:heading>

                @foreach ($options as $option)
                    <div class="rounded-xl border border-zinc-200 bg-white p-4">
                        <div class="flex items-center gap-4">
                            @if ($option->image_url)
                                <img src="{{ $option->image_url }}" alt="{{ $option->name }}" class="h-12 w-12 shrink-0 rounded-lg object-cover" onerror="this.style.display='none'" />
                            @else
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-zinc-100">
                                    <flux:icon.photo variant="mini" class="text-zinc-400" />
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-zinc-900">{{ $option->name }}</p>
                                @if ($option->description)
                                    <p class="mt-0.5 truncate text-sm text-zinc-500">{{ $option->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</div>
