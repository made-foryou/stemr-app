<?php

use App\Livewire\ManageOption;
use App\Models\Option;
use App\Models\Poll;
use App\Models\User;
use Livewire\Livewire;

test('manage option page is not accessible for guests', function () {
    $option = Option::factory()->create();

    $this->get(route('option.manage', [$option->poll, $option]))->assertRedirect(route('login'));
});

test('manage option page is accessible for the poll owner', function () {
    $option = Option::factory()->create();

    $this->actingAs($option->poll->user)
        ->get(route('option.manage', [$option->poll, $option]))
        ->assertOk();
});

test('manage option page is not accessible for other users', function () {
    $option = Option::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->get(route('option.manage', [$option->poll, $option]))
        ->assertForbidden();
});

test('manage option page returns 404 if option does not belong to poll', function () {
    $option = Option::factory()->create();
    $otherPoll = Poll::factory()->create(['user_id' => $option->poll->user_id]);

    $this->actingAs($option->poll->user)
        ->get(route('option.manage', [$otherPoll, $option]))
        ->assertNotFound();
});

test('manage option page shows option name', function () {
    $option = Option::factory()->create(['name' => 'Restaurant De Kas']);

    $this->actingAs($option->poll->user)
        ->get(route('option.manage', [$option->poll, $option]))
        ->assertSee('Restaurant De Kas');
});

test('manage option page title is the option name', function () {
    $option = Option::factory()->create(['name' => 'Cafe de Jaren']);

    $component = Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option]);

    expect($component->instance()->getTitle())->toBe('Cafe de Jaren');
});

test('manage option page shows link to parent poll', function () {
    $option = Option::factory()->create();

    $this->actingAs($option->poll->user)
        ->get(route('option.manage', [$option->poll, $option]))
        ->assertSee($option->poll->title);
});

// --- Edit option tests ---

test('can update option name', function () {
    $option = Option::factory()->create(['name' => 'Oude naam']);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('name', 'Nieuwe naam')
        ->call('save')
        ->assertDispatched('option-saved');

    expect($option->fresh()->name)->toBe('Nieuwe naam');
});

test('can update option image url', function () {
    $option = Option::factory()->create(['image_url' => null]);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('imageUrl', 'https://example.com/photo.jpg')
        ->call('save');

    expect($option->fresh()->image_url)->toBe('https://example.com/photo.jpg');
});

test('can update option description', function () {
    $option = Option::factory()->create(['description' => 'Oud']);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('description', 'Nieuw')
        ->call('save');

    expect($option->fresh()->description)->toBe('Nieuw');
});

test('can clear option description', function () {
    $option = Option::factory()->create(['description' => 'Heeft beschrijving']);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('description', '')
        ->call('save');

    expect($option->fresh()->description)->toBeNull();
});

test('can clear option image url', function () {
    $option = Option::factory()->create(['image_url' => 'https://example.com/old.jpg']);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('imageUrl', '')
        ->call('save');

    expect($option->fresh()->image_url)->toBeNull();
});

test('option name is required', function () {
    $option = Option::factory()->create();

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('option name max length is 100', function () {
    $option = Option::factory()->create();

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('name', str_repeat('a', 101))
        ->call('save')
        ->assertHasErrors(['name' => 'max']);
});

test('option image url must be a valid url', function () {
    $option = Option::factory()->create();

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('imageUrl', 'geen-url')
        ->call('save')
        ->assertHasErrors(['imageUrl' => 'url']);
});

test('option description max length is 500', function () {
    $option = Option::factory()->create();

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('description', str_repeat('a', 501))
        ->call('save')
        ->assertHasErrors(['description' => 'max']);
});

// --- Delete option tests ---

test('can delete an option', function () {
    $option = Option::factory()->create();
    $poll = $option->poll;
    $optionId = $option->id;

    Livewire::actingAs($poll->user)
        ->test(ManageOption::class, ['poll' => $poll, 'option' => $option])
        ->call('deleteOption')
        ->assertRedirect(route('poll.manage', $poll));

    expect(Option::find($optionId))->toBeNull();
});

test('manage option page shows delete button', function () {
    $option = Option::factory()->create();

    $this->actingAs($option->poll->user)
        ->get(route('option.manage', [$option->poll, $option]))
        ->assertSee(__('Verwijderen'));
});
