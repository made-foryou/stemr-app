<?php

namespace App\Livewire;

use App\Models\Option;
use App\Models\Poll;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class ManagePoll extends Component
{
    public Poll $poll;

    // Edit poll info
    public bool $editingPollInfo = false;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:5000')]
    public string $description = '';

    public function mount(Poll $poll): void
    {
        abort_unless($poll->user_id === auth()->id(), 403);

        $this->title = $poll->title;
        $this->description = $poll->description ?? '';
    }

    public function getTitle(): string
    {
        return $this->poll->title;
    }

    public function startEditingPollInfo(): void
    {
        $this->title = $this->poll->title;
        $this->description = $this->poll->description ?? '';
        $this->editingPollInfo = true;
    }

    public function savePollInfo(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
        ]);

        $this->poll->update([
            'title' => $this->title,
            'description' => $this->description ?: null,
        ]);

        $this->editingPollInfo = false;
    }

    public function cancelEditingPollInfo(): void
    {
        $this->title = $this->poll->title;
        $this->description = $this->poll->description ?? '';
        $this->editingPollInfo = false;
        $this->resetValidation(['title', 'description']);
    }

    public function deletePoll(): void
    {
        abort_unless($this->poll->user_id === auth()->id(), 403);

        $this->poll->delete();

        $this->redirect(route('dashboard'), navigate: true);
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

        $ordered = array_values(array_diff($ordered, [(int) $id]));
        array_splice($ordered, $position, 0, [(int) $id]);

        foreach ($ordered as $index => $optionId) {
            Option::where('id', $optionId)->update(['sort_order' => $index]);
        }

        unset($this->options);
    }

    #[Computed]
    public function options()
    {
        return $this->poll->options()->get();
    }

    public function render(): View
    {
        return view('livewire.manage-poll');
    }
}
