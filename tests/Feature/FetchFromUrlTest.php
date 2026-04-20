<?php

use App\Jobs\ScrapeUrl;
use App\Livewire\CreateOption;
use App\Livewire\CreatePoll;
use App\Livewire\ManageOption;
use App\Models\Option;
use App\Models\Poll;
use App\Models\ScrapeResult;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

function createCompletedScrapeResult(string $url, array $data = []): ScrapeResult
{
    return ScrapeResult::factory()->create(array_merge([
        'url' => $url,
        'status' => 'completed',
        'title' => 'Hotel Amsterdam',
        'description' => 'Mooi hotel in het centrum',
        'image_url' => 'https://example.com/photo.jpg',
    ], $data));
}

function createFailedScrapeResult(string $url, string $error = 'Geen metadata gevonden'): ScrapeResult
{
    return ScrapeResult::factory()->failed($error)->create(['url' => $url]);
}

// --- CreateOption: Job dispatching ---

test('CreateOption: dispatches scrape job when url is entered', function () {
    Queue::fake();
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('sourceUrl', 'https://example.com/hotel')
        ->assertSet('isFetchingUrl', true);

    Queue::assertPushed(ScrapeUrl::class, fn ($job) => $job->url === 'https://example.com/hotel');
});

test('CreateOption: returns existing result immediately without dispatching job', function () {
    Queue::fake();
    $poll = Poll::factory()->create();
    $url = 'https://example.com/cached';
    createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('sourceUrl', $url)
        ->assertSet('name', 'Hotel Amsterdam')
        ->assertSet('isFetchingUrl', false);

    Queue::assertNothingPushed();
});

test('CreateOption: re-scrapes when existing result is expired', function () {
    Queue::fake();
    config(['services.browserless.refresh_days' => 30]);

    $poll = Poll::factory()->create();
    $url = 'https://example.com/expired';

    ScrapeResult::factory()->create([
        'url' => $url,
        'status' => 'completed',
        'title' => 'Old Title',
        'created_at' => now()->subDays(31),
    ]);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('sourceUrl', $url)
        ->assertSet('isFetchingUrl', true);

    Queue::assertPushed(ScrapeUrl::class);
});

test('CreateOption: populates fields when polling detects completed scrape', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    $result = ScrapeResult::factory()->pending()->create(['url' => $url]);

    $result->update([
        'status' => 'completed',
        'title' => 'Hotel Amsterdam',
        'description' => 'Mooi hotel in het centrum',
        'image_url' => 'https://example.com/photo.jpg',
    ]);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->assertSet('name', 'Hotel Amsterdam')
        ->assertSet('description', 'Mooi hotel in het centrum')
        ->assertSet('imageUrl', 'https://example.com/photo.jpg')
        ->assertSet('isFetchingUrl', false);
});

test('CreateOption: shows suggestions when fields are already filled', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    $result = createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Mijn eigen naam')
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->assertSet('name', 'Mijn eigen naam')
        ->assertNotSet('scrapedData', [])
        ->assertSet('isFetchingUrl', false);
});

test('CreateOption: applySuggestion fills a specific field', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    $result = createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Mijn eigen naam')
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->call('applySuggestion', 'title')
        ->assertSet('name', 'Hotel Amsterdam');
});

test('CreateOption: applyAllSuggestions fills all fields', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    $result = createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Mijn eigen naam')
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->call('applyAllSuggestions')
        ->assertSet('name', 'Hotel Amsterdam')
        ->assertSet('description', 'Mooi hotel in het centrum')
        ->assertSet('imageUrl', 'https://example.com/photo.jpg');
});

test('CreateOption: dismissSuggestion removes a specific suggestion', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    $result = createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Mijn eigen naam')
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->call('dismissSuggestion', 'title')
        ->assertNotSet('scrapedData.title', 'Hotel Amsterdam');
});

test('CreateOption: dismissAllSuggestions clears all suggestions', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    $result = createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Mijn eigen naam')
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->call('dismissAllSuggestions')
        ->assertSet('scrapedData', []);
});

test('CreateOption: checkScrapeResult does nothing when not fetching', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('isFetchingUrl', false)
        ->set('scrapeResultId', null)
        ->call('checkScrapeResult')
        ->assertSet('isFetchingUrl', false)
        ->assertSet('name', '');
});

test('CreateOption: checkScrapeResult resets when record is deleted', function () {
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('scrapeResultId', 999999)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->assertSet('isFetchingUrl', false)
        ->assertSet('scrapeResultId', null);
});

test('CreateOption: applySuggestion ignores unknown field', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    $result = createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Mijn naam')
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->call('applySuggestion', 'nonexistent_field')
        ->assertSet('name', 'Mijn naam');
});

test('CreateOption: applySuggestion ignores empty scraped value', function () {
    $poll = Poll::factory()->create();

    $result = ScrapeResult::factory()->create([
        'url' => 'https://example.com/no-desc',
        'status' => 'completed',
        'title' => 'Titel',
        'description' => null,
        'image_url' => null,
    ]);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('name', 'Mijn naam')
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->call('applySuggestion', 'description')
        ->assertSet('description', '');
});

test('CreateOption: shows error on failed scrape', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/broken';
    $result = createFailedScrapeResult($url, 'Geen metadata gevonden');

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->assertSet('fetchError', 'Geen metadata gevonden')
        ->assertSet('isFetchingUrl', false);
});

test('CreateOption: saves source_url with option', function () {
    Queue::fake();
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('sourceUrl', $url)
        ->call('save');

    expect(Option::first())
        ->source_url->toBe($url)
        ->name->toBe('Hotel Amsterdam');
});

test('CreateOption: does not fetch for empty url', function () {
    Queue::fake();
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreateOption::class, ['poll' => $poll])
        ->set('sourceUrl', '')
        ->assertSet('name', '');

    Queue::assertNothingPushed();
});

// --- CreatePoll ---

test('CreatePoll: dispatches scrape job when url is entered', function () {
    Queue::fake();
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreatePoll::class)
        ->set('poll', $poll)
        ->set('step', 2)
        ->set('optionSourceUrl', 'https://example.com/hotel')
        ->assertSet('isFetchingUrl', true);

    Queue::assertPushed(ScrapeUrl::class);
});

test('CreatePoll: returns existing result immediately', function () {
    Queue::fake();
    $poll = Poll::factory()->create();
    $url = 'https://example.com/cached';
    createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreatePoll::class)
        ->set('poll', $poll)
        ->set('step', 2)
        ->set('optionSourceUrl', $url)
        ->assertSet('optionName', 'Hotel Amsterdam')
        ->assertSet('optionDescription', 'Mooi hotel in het centrum')
        ->assertSet('optionImageUrl', 'https://example.com/photo.jpg');

    Queue::assertNothingPushed();
});

test('CreatePoll: populates fields via polling', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    $result = createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreatePoll::class)
        ->set('poll', $poll)
        ->set('step', 2)
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->assertSet('optionName', 'Hotel Amsterdam')
        ->assertSet('isFetchingUrl', false);
});

test('CreatePoll: saves source_url when adding option', function () {
    Queue::fake();
    $poll = Poll::factory()->create();
    $url = 'https://example.com/hotel';
    createCompletedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreatePoll::class)
        ->set('poll', $poll)
        ->set('step', 2)
        ->set('optionSourceUrl', $url)
        ->call('addOption');

    expect(Option::first())
        ->source_url->toBe($url)
        ->name->toBe('Hotel Amsterdam');
});

test('CreatePoll: does not fetch for empty url', function () {
    Queue::fake();
    $poll = Poll::factory()->create();

    Livewire::actingAs($poll->user)
        ->test(CreatePoll::class)
        ->set('poll', $poll)
        ->set('step', 2)
        ->set('optionSourceUrl', '')
        ->assertSet('optionName', '');

    Queue::assertNothingPushed();
});

test('CreatePoll: shows error on failed fetch', function () {
    $poll = Poll::factory()->create();
    $url = 'https://example.com/broken';
    $result = createFailedScrapeResult($url);

    Livewire::actingAs($poll->user)
        ->test(CreatePoll::class)
        ->set('poll', $poll)
        ->set('step', 2)
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->assertNotSet('fetchError', '');
});

// --- ManageOption ---

test('ManageOption: dispatches scrape job when url is entered', function () {
    Queue::fake();
    $option = Option::factory()->create();

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('sourceUrl', 'https://example.com/hotel')
        ->assertSet('isFetchingUrl', true);

    Queue::assertPushed(ScrapeUrl::class);
});

test('ManageOption: shows suggestions when fields are already filled', function () {
    $option = Option::factory()->create(['name' => 'Bestaande naam']);
    $url = 'https://example.com/hotel';
    $result = createCompletedScrapeResult($url);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->assertSet('name', 'Bestaande naam')
        ->assertNotSet('scrapedData', []);
});

test('ManageOption: saves source_url when saving option', function () {
    Queue::fake();
    $option = Option::factory()->create();
    $url = 'https://example.com/hotel';
    createCompletedScrapeResult($url);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('sourceUrl', $url)
        ->call('applyAllSuggestions')
        ->call('save');

    expect($option->fresh())
        ->source_url->toBe($url)
        ->name->toBe('Hotel Amsterdam');
});

test('ManageOption: does not fetch for empty url', function () {
    Queue::fake();
    $option = Option::factory()->create();

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('sourceUrl', '')
        ->assertSet('name', $option->name);

    Queue::assertNothingPushed();
});

test('ManageOption: shows error on failed fetch', function () {
    $option = Option::factory()->create();
    $url = 'https://example.com/broken';
    $result = createFailedScrapeResult($url);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->set('scrapeResultId', $result->id)
        ->set('isFetchingUrl', true)
        ->call('checkScrapeResult')
        ->assertNotSet('fetchError', '');
});

test('ManageOption: loads existing source_url', function () {
    $option = Option::factory()->create(['source_url' => 'https://example.com/existing']);

    Livewire::actingAs($option->poll->user)
        ->test(ManageOption::class, ['poll' => $option->poll, 'option' => $option])
        ->assertSet('sourceUrl', 'https://example.com/existing');
});
