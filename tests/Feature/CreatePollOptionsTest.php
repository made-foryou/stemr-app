<?php

use App\Livewire\CreatePoll;
use App\Models\Option;
use App\Models\Poll;
use App\Models\User;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

function createPollAndGoToStep2(User $user): Testable
{
    return Livewire::actingAs($user)
        ->test(CreatePoll::class)
        ->set('title', 'Test poll')
        ->call('savePollInfo')
        ->assertSet('step', 2);
}

test('can add an option with name only', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Restaurant De Kas')
        ->call('addOption')
        ->assertSet('optionName', '');

    expect(Option::count())->toBe(1);
    expect(Option::first())
        ->name->toBe('Restaurant De Kas')
        ->image_url->toBeNull()
        ->description->toBeNull();
});

test('can add an option with all fields', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Restaurant De Kas')
        ->set('optionImageUrl', 'https://example.com/image.jpg')
        ->set('optionDescription', 'Een geweldig restaurant in Amsterdam')
        ->call('addOption');

    expect(Option::first())
        ->name->toBe('Restaurant De Kas')
        ->image_url->toBe('https://example.com/image.jpg')
        ->description->toBe('Een geweldig restaurant in Amsterdam');
});

test('option name is required', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', '')
        ->call('addOption')
        ->assertHasErrors(['optionName' => 'required']);

    expect(Option::count())->toBe(0);
});

test('option name cannot exceed 100 characters', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', str_repeat('a', 101))
        ->call('addOption')
        ->assertHasErrors(['optionName' => 'max']);
});

test('option image url must be valid url', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Test')
        ->set('optionImageUrl', 'not-a-url')
        ->call('addOption')
        ->assertHasErrors(['optionImageUrl' => 'url']);
});

test('option description cannot exceed 500 characters', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Test')
        ->set('optionDescription', str_repeat('a', 501))
        ->call('addOption')
        ->assertHasErrors(['optionDescription' => 'max']);
});

test('can edit an option', function () {
    $user = User::factory()->create();

    $component = createPollAndGoToStep2($user)
        ->set('optionName', 'Oorspronkelijke naam')
        ->call('addOption');

    $option = Option::first();

    $component
        ->call('editOption', $option->id)
        ->assertSet('optionName', 'Oorspronkelijke naam')
        ->assertSet('editingOptionId', $option->id)
        ->set('optionName', 'Nieuwe naam')
        ->call('updateOption');

    expect($option->fresh()->name)->toBe('Nieuwe naam');
});

test('can cancel editing an option', function () {
    $user = User::factory()->create();

    $component = createPollAndGoToStep2($user)
        ->set('optionName', 'Test optie')
        ->call('addOption');

    $option = Option::first();

    $component
        ->call('editOption', $option->id)
        ->assertSet('editingOptionId', $option->id)
        ->call('cancelEdit')
        ->assertSet('editingOptionId', null)
        ->assertSet('optionName', '');
});

test('can delete an option', function () {
    $user = User::factory()->create();

    $component = createPollAndGoToStep2($user)
        ->set('optionName', 'Te verwijderen optie')
        ->call('addOption');

    expect(Option::count())->toBe(1);

    $component->call('removeOption', Option::first()->id);

    expect(Option::count())->toBe(0);
});

test('options are assigned incrementing sort order', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Eerste')
        ->call('addOption')
        ->set('optionName', 'Tweede')
        ->call('addOption')
        ->set('optionName', 'Derde')
        ->call('addOption');

    $options = Option::orderBy('sort_order')->get();

    expect($options[0])->name->toBe('Eerste')->sort_order->toBe(1);
    expect($options[1])->name->toBe('Tweede')->sort_order->toBe(2);
    expect($options[2])->name->toBe('Derde')->sort_order->toBe(3);
});

test('can reorder options', function () {
    $user = User::factory()->create();

    $component = createPollAndGoToStep2($user)
        ->set('optionName', 'Eerste')
        ->call('addOption')
        ->set('optionName', 'Tweede')
        ->call('addOption')
        ->set('optionName', 'Derde')
        ->call('addOption');

    $options = Option::orderBy('sort_order')->get();

    // Move 'Derde' (id) to position 0 (first)
    $component->call('reorderOptions', (string) $options[2]->id, 0);

    $reordered = Option::orderBy('sort_order')->get();
    expect($reordered[0]->name)->toBe('Derde');
    expect($reordered[1]->name)->toBe('Eerste');
    expect($reordered[2]->name)->toBe('Tweede');
});

test('minimum 2 options required to finish', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Enige optie')
        ->call('addOption')
        ->call('finish')
        ->assertHasErrors('options')
        ->assertNotDispatched('redirect');
});

test('can finish with 2 or more options', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Optie een')
        ->call('addOption')
        ->set('optionName', 'Optie twee')
        ->call('addOption')
        ->call('finish')
        ->assertRedirect(route('poll.manage', Poll::first()));
});

test('options belong to the correct poll', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Mijn optie')
        ->call('addOption');

    $poll = Poll::first();
    $option = Option::first();

    expect($option->poll_id)->toBe($poll->id);
});

test('form is reset after adding an option', function () {
    $user = User::factory()->create();

    createPollAndGoToStep2($user)
        ->set('optionName', 'Test')
        ->set('optionImageUrl', 'https://example.com/img.jpg')
        ->set('optionDescription', 'Beschrijving')
        ->call('addOption')
        ->assertSet('optionName', '')
        ->assertSet('optionImageUrl', '')
        ->assertSet('optionDescription', '')
        ->assertSet('editingOptionId', null);
});
