<?php

namespace App\Livewire;

use App\Livewire\Concerns\ScrapesUrls;
use App\Models\Option;
use App\Models\Poll;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Poll aanmaken')]
class CreatePoll extends Component
{
    use ScrapesUrls;

    public int $step = 1;

    public ?Poll $poll = null;

    // Step 1: Poll info
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:5000')]
    public string $description = '';

    // Step 2: Option form
    public string $optionSourceUrl = '';

    public string $optionName = '';

    public string $optionImageUrl = '';

    public string $optionDescription = '';

    public ?int $editingOptionId = null;

    public bool $isFetchingUrl = false;

    public string $fetchError = '';

    public function savePollInfo(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
        ]);

        $this->poll = auth()->user()->polls()->create([
            'title' => $this->title,
            'description' => $this->description ?: null,
        ]);

        $this->step = 2;
    }

    public function updatedOptionSourceUrl(): void
    {
        $this->fetchError = '';

        if (empty($this->optionSourceUrl)) {
            return;
        }

        $this->validate([
            'optionSourceUrl' => 'url|max:2048',
        ]);

        $this->fetchFromUrl();
    }

    public function fetchFromUrl(): void
    {
        $this->dispatchScrapeJob($this->optionSourceUrl);
    }

    public function addOption(): void
    {
        $this->validate([
            'optionName' => 'required|string|max:100',
            'optionImageUrl' => 'nullable|url|max:2048',
            'optionSourceUrl' => 'nullable|url|max:2048',
            'optionDescription' => 'nullable|string|max:500',
        ]);

        $nextOrder = $this->poll->options()->max('sort_order') + 1;

        $this->poll->options()->create([
            'name' => $this->optionName,
            'image_url' => $this->optionImageUrl ?: null,
            'source_url' => $this->optionSourceUrl ?: null,
            'description' => $this->optionDescription ?: null,
            'sort_order' => $nextOrder,
        ]);

        $this->resetOptionForm();
        unset($this->options);
    }

    public function editOption(int $optionId): void
    {
        $option = $this->poll->options()->findOrFail($optionId);

        $this->editingOptionId = $option->id;
        $this->optionSourceUrl = $option->source_url ?? '';
        $this->optionName = $option->name;
        $this->optionImageUrl = $option->image_url ?? '';
        $this->optionDescription = $option->description ?? '';
    }

    public function updateOption(): void
    {
        $this->validate([
            'optionName' => 'required|string|max:100',
            'optionImageUrl' => 'nullable|url|max:2048',
            'optionSourceUrl' => 'nullable|url|max:2048',
            'optionDescription' => 'nullable|string|max:500',
        ]);

        $option = $this->poll->options()->findOrFail($this->editingOptionId);

        $option->update([
            'name' => $this->optionName,
            'image_url' => $this->optionImageUrl ?: null,
            'source_url' => $this->optionSourceUrl ?: null,
            'description' => $this->optionDescription ?: null,
        ]);

        $this->resetOptionForm();
        unset($this->options);
    }

    public function cancelEdit(): void
    {
        $this->resetOptionForm();
    }

    public function removeOption(int $optionId): void
    {
        $this->poll->options()->findOrFail($optionId)->delete();
        unset($this->options);
    }

    public function reorderOptions(string $id, int $position): void
    {
        $option = $this->poll->options()->findOrFail($id);

        $ordered = $this->poll->options()->pluck('id')->toArray();

        // Remove from current position and insert at new position
        $ordered = array_values(array_diff($ordered, [(int) $id]));
        array_splice($ordered, $position, 0, [(int) $id]);

        foreach ($ordered as $index => $optionId) {
            Option::where('id', $optionId)->update(['sort_order' => $index]);
        }

        unset($this->options);
    }

    public function finish(): void
    {
        if ($this->options->count() < 2) {
            $this->addError('options', __('Voeg minimaal 2 opties toe om door te gaan.'));

            return;
        }

        $this->redirect(route('poll.manage', $this->poll), navigate: true);
    }

    #[Computed]
    public function options()
    {
        return $this->poll?->options()->get() ?? collect();
    }

    public function render(): View
    {
        return view('livewire.create-poll');
    }

    protected function getScrapedFieldMapping(): array
    {
        return [
            'title' => 'optionName',
            'description' => 'optionDescription',
            'image_url' => 'optionImageUrl',
        ];
    }

    private function resetOptionForm(): void
    {
        $this->editingOptionId = null;
        $this->optionSourceUrl = '';
        $this->optionName = '';
        $this->optionImageUrl = '';
        $this->optionDescription = '';
        $this->fetchError = '';
        $this->scrapeResultId = null;
        $this->scrapedData = [];
        $this->isFetchingUrl = false;
        $this->resetValidation(['optionName', 'optionImageUrl', 'optionSourceUrl', 'optionDescription']);
    }
}
