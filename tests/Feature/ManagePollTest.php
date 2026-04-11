<?php

use App\Livewire\ManagePoll;
use App\Models\Option;
use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

test('manage poll page is not accessible for guests', function () {
    $poll = Poll::factory()->create();

    $this->get(route('poll.manage', $poll))->assertRedirect(route('login'));
});

test('manage poll page is accessible for the poll owner', function () {
    $poll = Poll::factory()->create();

    $this->actingAs($poll->user)
        ->get(route('poll.manage', $poll))
        ->assertOk();
});

test('manage poll page is not accessible for other users', function () {
    $poll = Poll::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->get(route('poll.manage', $poll))
        ->assertForbidden();
});

test('manage poll page shows poll title', function () {
    $poll = Poll::factory()->create(['title' => 'Teamuitje stemming']);

    $this->actingAs($poll->user)
        ->get(route('poll.manage', $poll))
        ->assertSee('Teamuitje stemming');
});

test('manage poll page title is the poll title', function () {
    $poll = Poll::factory()->create(['title' => 'Weekendtrip stemming']);

    $component = Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll]);

    expect($component->instance()->getTitle())->toBe('Weekendtrip stemming');
});

test('manage poll page shows options', function () {
    $poll = Poll::factory()->create();
    Option::factory()->for($poll)->create(['name' => 'Restaurant De Kas']);
    Option::factory()->for($poll)->create(['name' => 'Cafe de Jaren']);

    $this->actingAs($poll->user)
        ->get(route('poll.manage', $poll))
        ->assertSee('Restaurant De Kas')
        ->assertSee('Cafe de Jaren');
});

// --- Edit poll info tests ---

test('can start editing poll info', function () {
    $poll = Poll::factory()->create(['title' => 'Originele titel']);

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('startEditingPollInfo')
        ->assertSet('editingPollInfo', true)
        ->assertSet('title', 'Originele titel');
});

test('can update poll title', function () {
    $poll = Poll::factory()->create(['title' => 'Originele titel']);

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('startEditingPollInfo')
        ->set('title', 'Nieuwe titel')
        ->call('savePollInfo')
        ->assertSet('editingPollInfo', false);

    expect($poll->fresh()->title)->toBe('Nieuwe titel');
});

test('can update poll description', function () {
    $poll = Poll::factory()->create(['description' => 'Oude beschrijving']);

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('startEditingPollInfo')
        ->set('description', 'Nieuwe beschrijving')
        ->call('savePollInfo');

    expect($poll->fresh()->description)->toBe('Nieuwe beschrijving');
});

test('can clear poll description', function () {
    $poll = Poll::factory()->create(['description' => 'Heeft een beschrijving']);

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('startEditingPollInfo')
        ->set('description', '')
        ->call('savePollInfo');

    expect($poll->fresh()->description)->toBeNull();
});

test('poll title is required when editing', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('startEditingPollInfo')
        ->set('title', '')
        ->call('savePollInfo')
        ->assertHasErrors(['title' => 'required']);
});

test('poll title max length is 255 when editing', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('startEditingPollInfo')
        ->set('title', str_repeat('a', 256))
        ->call('savePollInfo')
        ->assertHasErrors(['title' => 'max']);
});

test('can cancel editing poll info', function () {
    $poll = Poll::factory()->create(['title' => 'Originele titel']);

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('startEditingPollInfo')
        ->set('title', 'Gewijzigde titel')
        ->call('cancelEditingPollInfo')
        ->assertSet('editingPollInfo', false)
        ->assertSet('title', 'Originele titel');

    expect($poll->fresh()->title)->toBe('Originele titel');
});

// --- Delete poll tests ---

test('can delete a poll', function () {
    $poll = Poll::factory()->create();
    $pollId = $poll->id;

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('deletePoll')
        ->assertRedirect(route('dashboard'));

    expect(Poll::find($pollId))->toBeNull();
});

test('deleting a poll also deletes its options', function () {
    $poll = Poll::factory()->create();
    Option::factory()->for($poll)->count(3)->create();

    expect(Option::count())->toBe(3);

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('deletePoll');

    expect(Option::count())->toBe(0);
});

test('other users cannot delete a poll', function () {
    $poll = Poll::factory()->create();
    $otherUser = User::factory()->create();

    Livewire::actingAs($otherUser)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->assertForbidden();
});

// --- Option management tests ---

test('can remove an option from manage page', function () {
    $poll = Poll::factory()->create();
    $option = Option::factory()->for($poll)->create();

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('removeOption', $option->id);

    expect($poll->options()->count())->toBe(0);
});

test('can reorder options from manage page', function () {
    $poll = Poll::factory()->create();
    $option1 = Option::factory()->for($poll)->create(['name' => 'Eerste', 'sort_order' => 0]);
    $option2 = Option::factory()->for($poll)->create(['name' => 'Tweede', 'sort_order' => 1]);
    $option3 = Option::factory()->for($poll)->create(['name' => 'Derde', 'sort_order' => 2]);

    Livewire::actingAs($poll->user)
        ->test(ManagePoll::class, ['poll' => $poll])
        ->call('reorderOptions', $option3->id, 0);

    expect($option3->fresh()->sort_order)->toBe(0);
    expect($option1->fresh()->sort_order)->toBe(1);
    expect($option2->fresh()->sort_order)->toBe(2);
});

test('manage page shows edit and delete buttons', function () {
    $poll = Poll::factory()->create();

    $this->actingAs($poll->user)
        ->get(route('poll.manage', $poll))
        ->assertSee(__('Bewerken'))
        ->assertSee(__('Verwijderen'));
});

test('manage page shows add option button', function () {
    $poll = Poll::factory()->create();

    $this->actingAs($poll->user)
        ->get(route('poll.manage', $poll))
        ->assertSee(__('Optie toevoegen'));
});

test('manage page shows empty state when no options', function () {
    $poll = Poll::factory()->create();

    $this->actingAs($poll->user)
        ->get(route('poll.manage', $poll))
        ->assertSee(__('Deze poll heeft nog geen opties.'));
});
