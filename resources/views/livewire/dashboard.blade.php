<div class="min-h-screen">
    <x-app-header>
        <x-slot:actions>
            <a href="{{ route('account.settings') }}" wire:navigate>
                <flux:button variant="subtle" size="sm" icon="cog-6-tooth">
                    {{ __('Account') }}
                </flux:button>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="subtle" type="submit" size="sm">
                    {{ __('Uitloggen') }}
                </flux:button>
            </form>
        </x-slot:actions>
    </x-app-header>

    <main class="mx-auto max-w-6xl px-6 py-12 lg:py-16">
        @if($polls->isEmpty())
            {{-- ═══════════════════════════════════════════════════ --}}
            {{-- EMPTY STATE — orbital graphic + welkomstbericht    --}}
            {{-- ═══════════════════════════════════════════════════ --}}
            <div class="grid items-center gap-12 lg:grid-cols-[1fr_1.1fr] lg:gap-20">

                {{-- Animated orbital graphic --}}
                <div class="relative mx-auto aspect-square w-full max-w-md animate-fade-in">
                    <div class="absolute left-1/2 top-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl vota-pulse" style="background-color: rgba(107, 53, 104, 0.08);"></div>

                    <div class="absolute inset-8 rounded-full border border-zinc-300/30"></div>
                    <div class="absolute inset-20 rounded-full border border-zinc-300/15"></div>

                    <div class="absolute left-1/2 top-1/2 flex h-20 w-20 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full shadow-lg vota-breathe" style="background-color: var(--color-vota-primary); box-shadow: 0 10px 25px rgba(107, 53, 104, 0.2);">
                        <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>

                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 25s;">
                        <div class="absolute left-1/2 top-0 h-4 w-4 -translate-x-1/2 rounded-full shadow-md vota-breathe" style="background-color: var(--color-vota-primary); box-shadow: 0 4px 12px rgba(107, 53, 104, 0.25); animation-delay: 0.5s;"></div>
                    </div>
                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 30s; animation-direction: reverse;">
                        <div class="absolute left-1/2 top-0 h-3 w-3 -translate-x-1/2 rounded-full shadow-md vota-breathe" style="background-color: var(--color-vota-accent); box-shadow: 0 4px 12px rgba(196, 112, 63, 0.25); animation-delay: 1.2s;"></div>
                    </div>
                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 20s;">
                        <div class="absolute left-0 top-1/2 h-3.5 w-3.5 -translate-y-1/2 rounded-full shadow-md vota-breathe" style="background-color: #8B4E88; box-shadow: 0 4px 12px rgba(139, 78, 136, 0.25); animation-delay: 0.8s;"></div>
                    </div>

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

                    <svg class="absolute inset-0 h-full w-full opacity-[0.06]" viewBox="0 0 400 400">
                        <line x1="200" y1="120" x2="300" y2="80" stroke="#6B3568" stroke-width="1" />
                        <line x1="200" y1="120" x2="100" y2="180" stroke="#6B3568" stroke-width="1" />
                        <line x1="200" y1="120" x2="280" y2="300" stroke="#6B3568" stroke-width="1" />
                    </svg>
                </div>

                {{-- Welcome message + CTA --}}
                <div>
                    <div class="animate-fade-in">
                        <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight lg:!text-4xl font-display">
                            {{ __('Welkom, :name', ['name' => auth()->user()->name]) }}
                        </flux:heading>
                        <flux:subheading class="mt-3 !text-lg !text-zinc-500">
                            {{ __('Je hebt nog geen polls aangemaakt.') }}
                        </flux:subheading>
                    </div>

                    <div class="mt-10 animate-slide-up vota-card p-8" style="animation-delay: 0.15s;">
                        <div class="flex flex-col items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl" style="background-color: var(--color-vota-primary-light);">
                                <flux:icon.plus class="h-6 w-6" style="color: var(--color-vota-primary);" />
                            </div>
                            <div>
                                <flux:heading size="lg" class="!font-semibold">{{ __('Maak je eerste poll') }}</flux:heading>
                                <flux:subheading class="mt-1 !text-zinc-500">
                                    {{ __('Maak opties aan, nodig je team uit, en ontdek de favoriet.') }}
                                </flux:subheading>
                            </div>
                            <a href="{{ route('poll.create') }}" wire:navigate>
                                <flux:button variant="primary" class="mt-2">
                                    {{ __('Poll aanmaken') }}
                                </flux:button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- ═══════════════════════════════════════════════════ --}}
            {{-- POLL LIST — overzicht van alle polls               --}}
            {{-- ═══════════════════════════════════════════════════ --}}

            {{-- Page heading --}}
            <div class="flex items-center justify-between animate-fade-in">
                <div>
                    <flux:heading size="xl" class="!text-3xl !font-bold !tracking-tight font-display">
                        {{ __('Mijn polls') }}
                    </flux:heading>
                    <flux:subheading class="mt-1 !text-zinc-500">
                        {{ trans_choice(':count poll|:count polls', $polls->count(), ['count' => $polls->count()]) }}
                    </flux:subheading>
                </div>

                <a href="{{ route('poll.create') }}" wire:navigate>
                    <flux:button variant="primary" icon="plus">
                        {{ __('Nieuwe poll') }}
                    </flux:button>
                </a>
            </div>

            {{-- Poll cards --}}
            <div class="mt-8 space-y-4">
                @foreach($polls as $poll)
                    <a href="{{ route('poll.manage', $poll) }}"
                       class="group block animate-slide-up vota-card vota-card-interactive p-6"
                       style="animation-delay: {{ $loop->index * 0.06 }}s;"
                       wire:navigate>
                        <div class="flex items-center justify-between gap-4">
                            {{-- Left: title + meta --}}
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-lg font-semibold tracking-tight transition-colors duration-200" style="color: var(--color-vota-text);">
                                    {{ $poll->title }}
                                </h3>
                                <p class="mt-1 text-sm text-zinc-400">
                                    {{ $poll->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Right: status + counts --}}
                            <div class="flex shrink-0 items-center gap-4">
                                {{-- Placeholder counts --}}
                                <div class="hidden items-center gap-4 text-sm text-zinc-400 sm:flex">
                                    <span class="flex items-center gap-1.5">
                                        <flux:icon.envelope class="h-4 w-4" />
                                        <span>—</span>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <flux:icon.heart class="h-4 w-4" />
                                        <span>—</span>
                                    </span>
                                </div>

                                {{-- Status badge --}}
                                @if($poll->isOpen())
                                    <flux:badge color="green" size="sm">{{ __('Open') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ __('Gesloten') }}</flux:badge>
                                @endif

                                {{-- Arrow --}}
                                <flux:icon.chevron-right class="h-5 w-5 text-zinc-300 transition-transform duration-200 group-hover:translate-x-0.5" />
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </main>
</div>
