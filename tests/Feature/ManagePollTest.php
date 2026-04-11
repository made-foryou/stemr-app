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
