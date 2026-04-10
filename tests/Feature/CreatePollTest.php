<?php

use App\Enums\PollStatus;
use App\Livewire\CreatePoll;
use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

test('create poll page is not accessible for guests', function () {
    $this->get(route('poll.create'))->assertRedirect(route('login'));
});

test('create poll page is accessible for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('poll.create'))->assertOk();
});

test('a poll can be created with a title', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Beste restaurant voor teamuitje')
        ->call('save')
        ->assertRedirect(route('poll.manage', Poll::first()));

    expect(Poll::count())->toBe(1);
    expect(Poll::first())
        ->title->toBe('Beste restaurant voor teamuitje')
        ->description->toBeNull()
        ->status->toBe(PollStatus::Open)
        ->user_id->toBe($user->id)
        ->slug->toBe('beste-restaurant-voor-teamuitje')
        ->uuid->not->toBeNull();
});

test('a poll can be created with a title and description', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Teamuitje stemming')
        ->set('description', 'Stem op je favoriete locatie voor het teamuitje.')
        ->call('save')
        ->assertRedirect(route('poll.manage', Poll::first()));

    expect(Poll::first())
        ->title->toBe('Teamuitje stemming')
        ->description->toBe('Stem op je favoriete locatie voor het teamuitje.');
});

test('title is required to create a poll', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);

    expect(Poll::count())->toBe(0);
});

test('title cannot exceed 255 characters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['title' => 'max']);
});

test('description cannot exceed 5000 characters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Test poll')
        ->set('description', str_repeat('a', 5001))
        ->call('save')
        ->assertHasErrors(['description' => 'max']);
});

test('poll is linked to the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Mijn poll')
        ->call('save');

    expect(Poll::first()->user_id)->toBe($user->id);
    expect($otherUser->polls)->toHaveCount(0);
});

test('poll defaults to open status', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Status test')
        ->call('save');

    expect(Poll::first()->isOpen())->toBeTrue();
});

test('poll gets a unique slug', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Dezelfde titel')
        ->call('save');

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Dezelfde titel')
        ->call('save');

    $polls = Poll::orderBy('id')->get();
    expect($polls[0]->slug)->toBe('dezelfde-titel');
    expect($polls[1]->slug)->toBe('dezelfde-titel-2');
});

test('poll is visible on dashboard after creation', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Mijn nieuwe poll')
        ->call('save');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSee('Mijn nieuwe poll');
});
