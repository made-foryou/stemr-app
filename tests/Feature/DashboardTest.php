<?php

use App\Models\Poll;
use App\Models\User;

test('dashboard is not accessible for guests', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('dashboard shows empty state when user has no polls', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('Je hebt nog geen polls aangemaakt.'));
    $response->assertSee(__('Maak je eerste poll'));
});

test('dashboard shows polls belonging to the user', function () {
    $user = User::factory()->create();
    $poll = Poll::factory()->for($user)->create(['title' => 'Beste Teamuitje']);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Beste Teamuitje');
    $response->assertSee(__('Mijn polls'));
});

test('dashboard does not show polls of other users', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Poll::factory()->for($otherUser)->create(['title' => 'Geheime Poll']);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertDontSee('Geheime Poll');
});

test('dashboard shows polls sorted newest first', function () {
    $user = User::factory()->create();
    $oldPoll = Poll::factory()->for($user)->create([
        'title' => 'Oude Poll',
        'created_at' => now()->subDays(5),
    ]);
    $newPoll = Poll::factory()->for($user)->create([
        'title' => 'Nieuwe Poll',
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSeeInOrder(['Nieuwe Poll', 'Oude Poll']);
});

test('dashboard shows correct status badges', function () {
    $user = User::factory()->create();
    Poll::factory()->for($user)->create(['title' => 'Open Poll']);
    Poll::factory()->for($user)->closed()->create(['title' => 'Gesloten Poll']);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('Open'));
    $response->assertSee(__('Gesloten'));
});

test('dashboard shows new poll button when polls exist', function () {
    $user = User::factory()->create();
    Poll::factory()->for($user)->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(__('Nieuwe poll'));
});
