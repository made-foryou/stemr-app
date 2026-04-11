<?php

use App\Models\Option;
use App\Models\Poll;

test('option belongs to a poll', function () {
    $option = Option::factory()->create();

    expect($option->poll)->toBeInstanceOf(Poll::class);
});

test('option route key name is uuid', function () {
    $option = new Option;

    expect($option->getRouteKeyName())->toBe('uuid');
});

test('option generates uuid on creation', function () {
    $option = Option::factory()->create();

    expect($option->uuid)->not->toBeNull();
    expect(strlen($option->uuid))->toBe(36);
});

test('option has fillable attributes', function () {
    $poll = Poll::factory()->create();

    $option = $poll->options()->create([
        'name' => 'Restaurant De Kas',
        'image_url' => 'https://example.com/image.jpg',
        'description' => 'Een geweldig restaurant',
        'sort_order' => 1,
    ]);

    expect($option)
        ->name->toBe('Restaurant De Kas')
        ->image_url->toBe('https://example.com/image.jpg')
        ->description->toBe('Een geweldig restaurant')
        ->sort_order->toBe(1);
});

test('poll has many options ordered by sort_order', function () {
    $poll = Poll::factory()->create();

    Option::factory()->for($poll)->create(['name' => 'Tweede', 'sort_order' => 2]);
    Option::factory()->for($poll)->create(['name' => 'Eerste', 'sort_order' => 1]);
    Option::factory()->for($poll)->create(['name' => 'Derde', 'sort_order' => 3]);

    $options = $poll->options;

    expect($options)->toHaveCount(3);
    expect($options[0]->name)->toBe('Eerste');
    expect($options[1]->name)->toBe('Tweede');
    expect($options[2]->name)->toBe('Derde');
});

test('options are deleted when poll is deleted', function () {
    $poll = Poll::factory()->create();
    Option::factory()->for($poll)->count(3)->create();

    expect(Option::count())->toBe(3);

    $poll->delete();

    expect(Option::count())->toBe(0);
});
