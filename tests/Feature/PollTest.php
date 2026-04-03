<?php

use App\Enums\PollStatus;
use App\Models\Poll;
use App\Models\User;

test('poll belongs to a user', function () {
    $poll = Poll::factory()->create();

    expect($poll->user)->toBeInstanceOf(User::class);
});

test('poll route key name is slug', function () {
    $poll = new Poll;

    expect($poll->getRouteKeyName())->toBe('slug');
});

test('poll generates uuid on creation', function () {
    $poll = Poll::factory()->create();

    expect($poll->uuid)->not->toBeNull();
    expect(strlen($poll->uuid))->toBe(36);
});

test('poll generates slug from title', function () {
    $poll = Poll::factory()->create(['title' => 'Beste Restaurant Keuze']);

    expect($poll->slug)->toBe('beste-restaurant-keuze');
});

test('poll generates unique slug when duplicate title exists', function () {
    $user = User::factory()->create();

    $poll1 = Poll::factory()->for($user)->create(['title' => 'Team Uitje']);
    $poll2 = Poll::factory()->for($user)->create(['title' => 'Team Uitje']);

    expect($poll1->slug)->toBe('team-uitje');
    expect($poll2->slug)->toBe('team-uitje-2');
});

test('poll can determine if it is open', function () {
    $openPoll = Poll::factory()->create();
    $closedPoll = Poll::factory()->closed()->create();

    expect($openPoll->isOpen())->toBeTrue();
    expect($openPoll->isClosed())->toBeFalse();
    expect($closedPoll->isOpen())->toBeFalse();
    expect($closedPoll->isClosed())->toBeTrue();
});

test('poll casts status to enum', function () {
    $poll = Poll::factory()->create();

    expect($poll->status)->toBeInstanceOf(PollStatus::class);
    expect($poll->status)->toBe(PollStatus::Open);
});

test('user has many polls', function () {
    $user = User::factory()->create();
    Poll::factory()->for($user)->count(3)->create();

    expect($user->polls)->toHaveCount(3);
    expect($user->polls->first())->toBeInstanceOf(Poll::class);
});
