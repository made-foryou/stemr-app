<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Poll aanmaken')]
class CreatePoll extends Component
{
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:5000')]
    public string $description = '';

    public function save(): void
    {
        $this->validate();

        $poll = auth()->user()->polls()->create([
            'title' => $this->title,
            'description' => $this->description ?: null,
        ]);

        $this->redirect(route('poll.manage', $poll), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.create-poll');
    }
}
