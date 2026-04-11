<?php

use App\Livewire\CreateOption;
use App\Models\Option;
use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

test('create option page is not accessible for guests', function () {
    $poll = Poll::factory()->create();

    $this->get(route('option.create', $poll))->assertRedirect(route('login'));
});

test('create option page is accessible for the poll owner', function () {
    $poll = Poll::factory()->create();

    $this->actingAs($poll->user)
        ->get(route('option.create', $poll))
        ->assertOk();
});

test('create option page is not accessible for other users', function () {
    $poll = Poll::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->get(route('option.create', $poll))
        ->assertForbidden();
});

test('create option page shows poll title', function () {
    $poll = Poll::factory()->create(['title' => 'Teamuitje stemming']);

    $this->actingAs($poll->user)
        ->get(route('option.create', $poll))
        ->assertSee('Teamuitje stemming');
});

test('can create an option with name only', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Restaurant De Kas')
        ->call('save');

    expect(Option::count())->toBe(1);
    expect(Option::first())
        ->name->toBe('Restaurant De Kas')
        ->image_url->toBeNull()
        ->description->toBeNull();
});

test('can create an option with all fields', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Restaurant De Kas')
        ->set('imageUrl', 'https://example.com/photo.jpg')
        ->set('description', 'Fijn restaurant in Amsterdam')
        ->call('save');

    expect(Option::first())
        ->name->toBe('Restaurant De Kas')
        ->image_url->toBe('https://example.com/photo.jpg')
        ->description->toBe('Fijn restaurant in Amsterdam');
});

test('creating an option redirects to option manage page', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Nieuwe optie')
        ->call('save')
        ->assertRedirect(route('option.manage', [$poll, Option::first()]));
});

test('option gets correct sort order', function () {
    $poll = Poll::factory()->create();
    Option::factory()->for($poll)->create(['sort_order' => 0]);
    Option::factory()->for($poll)->create(['sort_order' => 1]);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Derde optie')
        ->call('save');

    expect(Option::where('name', 'Derde optie')->first()->sort_order)->toBe(2);
});

test('option belongs to the correct poll', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Nieuwe optie')
        ->call('save');

    expect(Option::first()->poll_id)->toBe($poll->id);
});

// --- Validation tests ---

test('option name is required', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('option name max length is 100', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', str_repeat('a', 101))
        ->call('save')
        ->assertHasErrors(['name' => 'max']);
});

test('option image url must be a valid url', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Test')
        ->set('imageUrl', 'geen-url')
        ->call('save')
        ->assertHasErrors(['imageUrl' => 'url']);
});

test('option description max length is 500', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Test')
        ->set('description', str_repeat('a', 501))
        ->call('save')
        ->assertHasErrors(['description' => 'max']);
});
