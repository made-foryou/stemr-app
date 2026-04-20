@props(['value', 'field'])

<div class="flex items-center gap-2 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm animate-fade-in">
    <flux:icon.sparkles variant="mini" class="shrink-0 text-[var(--color-vota-primary)]" />
    <span class="min-w-0 flex-1 truncate text-zinc-600">{{ Str::limit($value, 60) }}</span>
    <button type="button" wire:click="applySuggestion('{{ $field }}')"
        class="shrink-0 font-medium text-[var(--color-vota-primary)] transition-colors hover:text-[var(--color-vota-primary-dark)]">
        {{ __('Toepassen') }}
    </button>
    <button type="button" wire:click="dismissSuggestion('{{ $field }}')"
        class="shrink-0 text-zinc-400 transition-colors hover:text-zinc-600">
        <flux:icon.x-mark variant="micro" />
    </button>
</div>
