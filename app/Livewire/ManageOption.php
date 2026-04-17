<?php

namespace App\Livewire;

use App\Livewire\Concerns\ScrapesUrls;
use App\Models\Option;
use App\Models\Poll;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class ManageOption extends Component
{
    use ScrapesUrls;

    public Option $option;

    #[Validate('nullable|url|max:2048')]
    public string $sourceUrl = '';

    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('nullable|url|max:2048')]
    public string $imageUrl = '';

    #[Validate('nullable|string|max:500')]
    public string $description = '';

    public bool $isFetchingUrl = false;

    public string $fetchError = '';

    public function mount(Poll $poll, Option $option): void
    {
        abort_unless($option->poll_id === $poll->id, 404);
        abort_unless($poll->user_id === auth()->id(), 403);

        $this->sourceUrl = $option->source_url ?? '';
        $this->name = $option->name;
        $this->imageUrl = $option->image_url ?? '';
        $this->description = $option->description ?? '';
    }

    public function getTitle(): string
    {
        return $this->option->name;
    }

    public function updatedSourceUrl(): void
    {
        $this->fetchError = '';

        if (empty($this->sourceUrl)) {
            return;
        }

        $this->validate([
            'sourceUrl' => 'url|max:2048',
        ]);

        $this->fetchFromUrl();
    }

    public function fetchFromUrl(): void
    {
        $this->dispatchScrapeJob($this->sourceUrl);
    }

    public function save(): void
    {
        $this->validate();

        $this->option->update([
            'name' => $this->name,
            'image_url' => $this->imageUrl ?: null,
            'source_url' => $this->sourceUrl ?: null,
            'description' => $this->description ?: null,
        ]);

        $this->dispatch('option-saved');
    }

    public function deleteOption(): void
    {
        abort_unless($this->option->poll->user_id === auth()->id(), 403);

        $poll = $this->option->poll;

        $this->option->delete();

        $this->redirect(route('poll.manage', $poll), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.manage-option');
    }

    protected function getScrapedFieldMapping(): array
    {
        return [
            'title' => 'name',
            'description' => 'description',
            'image_url' => 'imageUrl',
        ];
    }
}
