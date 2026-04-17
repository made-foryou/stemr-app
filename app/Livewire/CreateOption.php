<?php

namespace App\Livewire;

use App\Livewire\Concerns\ScrapesUrls;
use App\Models\Poll;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Optie toevoegen')]
class CreateOption extends Component
{
    use ScrapesUrls;

    public Poll $poll;

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

    public function mount(Poll $poll): void
    {
        abort_unless($poll->user_id === auth()->id(), 403);
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

        $nextOrder = $this->poll->options()->max('sort_order') + 1;

        $option = $this->poll->options()->create([
            'name' => $this->name,
            'image_url' => $this->imageUrl ?: null,
            'source_url' => $this->sourceUrl ?: null,
            'description' => $this->description ?: null,
            'sort_order' => $nextOrder,
        ]);

        $this->redirect(route('option.manage', [$this->poll, $option]), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.create-option');
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
