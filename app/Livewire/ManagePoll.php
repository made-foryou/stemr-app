<?php

namespace App\Livewire;

use App\Models\Poll;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ManagePoll extends Component
{
    public Poll $poll;

    public function mount(Poll $poll): void
    {
        abort_unless($poll->user_id === auth()->id(), 403);
    }

    public function getTitle(): string
    {
        return $this->poll->title;
    }

    public function render(): View
    {
        return view('livewire.manage-poll', [
            'options' => $this->poll->options()->get(),
        ]);
    }
}
