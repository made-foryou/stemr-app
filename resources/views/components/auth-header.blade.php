@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1.5">
    <flux:heading size="xl" class="!text-2xl !font-bold !tracking-tight">{{ $title }}</flux:heading>
    <flux:subheading class="!text-zinc-500">{{ $description }}</flux:subheading>
</div>
