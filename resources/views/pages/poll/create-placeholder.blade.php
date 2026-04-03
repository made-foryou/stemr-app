<x-layouts::app :title="__('Nieuwe poll')">
    <div class="flex min-h-screen items-center justify-center">
        <div class="animate-fade-in text-center">
            <flux:heading size="xl" class="!font-bold">{{ __('Binnenkort beschikbaar') }}</flux:heading>
            <flux:subheading class="mt-2 !text-zinc-500">{{ __('Poll aanmaken wordt binnenkort toegevoegd.') }}</flux:subheading>
            <a href="{{ route('dashboard') }}" class="mt-6 inline-block" wire:navigate>
                <flux:button variant="primary">{{ __('Terug naar dashboard') }}</flux:button>
            </a>
        </div>
    </div>
</x-layouts::app>
