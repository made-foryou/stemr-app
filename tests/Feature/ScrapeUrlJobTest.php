<?php

use App\Jobs\ScrapeUrl;
use App\Models\ScrapeResult;
use App\Services\UrlScraper;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['services.scrapingbee.api_key' => null]);
});

test('job scrapes url and updates result to completed', function () {
    Http::fake([
        'https://example.com/hotel' => Http::response('<html><head>
            <meta property="og:title" content="Hotel Amsterdam" />
            <meta property="og:description" content="Mooi hotel" />
            <meta property="og:image" content="https://example.com/photo.jpg" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::factory()->pending()->create([
        'url' => 'https://example.com/hotel',
    ]);

    (new ScrapeUrl('https://example.com/hotel', $result->id))->handle(app(UrlScraper::class));

    $result->refresh();

    expect($result)
        ->status->toBe('completed')
        ->title->toBe('Hotel Amsterdam')
        ->description->toBe('Mooi hotel')
        ->image_url->toBe('https://example.com/photo.jpg')
        ->html_response->toContain('Hotel Amsterdam');
});

test('job sets failed status when all methods fail', function () {
    Http::fake([
        'https://example.com/empty' => Http::response('<html><head></head><body></body></html>'),
    ]);

    $result = ScrapeResult::factory()->pending()->create([
        'url' => 'https://example.com/empty',
    ]);

    (new ScrapeUrl('https://example.com/empty', $result->id))->handle(app(UrlScraper::class));

    $result->refresh();

    expect($result)
        ->status->toBe('failed')
        ->error_message->toContain('Geen metadata gevonden');
});

test('job uses ScrapingBee cascade with premium proxy fallback', function () {
    config(['services.scrapingbee.api_key' => 'test-key']);

    Http::fake([
        'https://example.com/booking' => Http::response('<html><head></head><body></body></html>'),
        'https://app.scrapingbee.com/api/v1*' => Http::sequence()
            ->push('Blocked', 500)
            ->push('<html><head><meta property="og:title" content="Premium Result" /></head><body></body></html>'),
    ]);

    $result = ScrapeResult::factory()->pending()->create([
        'url' => 'https://example.com/booking',
    ]);

    (new ScrapeUrl('https://example.com/booking', $result->id))->handle(app(UrlScraper::class));

    $result->refresh();

    expect($result)
        ->status->toBe('completed')
        ->title->toBe('Premium Result')
        ->used_scrapingbee->toBeTrue()
        ->used_premium_proxy->toBeTrue();
});

test('job never leaves result as pending', function () {
    Http::fake([
        'https://example.com/crash' => Http::response('Server Error', 500),
    ]);

    $result = ScrapeResult::factory()->pending()->create([
        'url' => 'https://example.com/crash',
    ]);

    (new ScrapeUrl('https://example.com/crash', $result->id))->handle(app(UrlScraper::class));

    $result->refresh();

    expect($result->status)->not->toBe('pending');
});

test('job skips scraping if a valid result already exists for the URL', function () {
    Http::fake();

    ScrapeResult::factory()->create([
        'url' => 'https://example.com/hotel',
        'status' => 'completed',
        'title' => 'Cached Title',
        'description' => 'Cached description',
        'image_url' => 'https://example.com/cached.jpg',
        'created_at' => now()->subDays(5),
    ]);

    $pending = ScrapeResult::factory()->pending()->create([
        'url' => 'https://example.com/hotel?ref=abc',
    ]);

    (new ScrapeUrl('https://example.com/hotel?ref=abc', $pending->id))->handle(app(UrlScraper::class));

    $pending->refresh();

    expect($pending)
        ->status->toBe('completed')
        ->title->toBe('Cached Title');

    Http::assertNothingSent();
});

test('job catches unexpected exception and sets result to failed', function () {
    $result = ScrapeResult::factory()->pending()->create([
        'url' => 'https://example.com/explode',
    ]);

    $scraper = Mockery::mock(UrlScraper::class);
    $scraper->shouldReceive('findOrCreate')->andReturnNull();
    $scraper->shouldReceive('scrapeAndUpdate')->andThrow(new RuntimeException('Connection timed out'));

    (new ScrapeUrl('https://example.com/explode', $result->id))->handle($scraper);

    $result->refresh();

    expect($result)
        ->status->toBe('failed')
        ->error_message->toBe('Connection timed out');
});

test('job does not overwrite status if scrapeAndUpdate already set it to failed', function () {
    $result = ScrapeResult::factory()->pending()->create([
        'url' => 'https://example.com/already-failed',
    ]);

    $scraper = Mockery::mock(UrlScraper::class);
    $scraper->shouldReceive('findOrCreate')->andReturnNull();
    $scraper->shouldReceive('scrapeAndUpdate')->andReturnUsing(function (ScrapeResult $r) {
        $r->update(['status' => 'failed', 'error_message' => 'Geen metadata gevonden']);
        throw new RuntimeException('Geen metadata gevonden');
    });

    (new ScrapeUrl('https://example.com/already-failed', $result->id))->handle($scraper);

    $result->refresh();

    expect($result)
        ->status->toBe('failed')
        ->error_message->toBe('Geen metadata gevonden');
});

test('job skips if result is no longer pending', function () {
    Http::fake();

    $result = ScrapeResult::factory()->create([
        'url' => 'https://example.com/already-done',
        'status' => 'completed',
    ]);

    (new ScrapeUrl('https://example.com/already-done', $result->id))->handle(app(UrlScraper::class));

    Http::assertNothingSent();
});

test('job implements ShouldBeUnique', function () {
    $job = new ScrapeUrl('https://example.com/hotel', 1);

    expect($job)->toBeInstanceOf(ShouldBeUnique::class);
});

test('job unique id is based on URL without query params', function () {
    $job1 = new ScrapeUrl('https://booking.com/hotel/amsterdam?aid=123', 1);
    $job2 = new ScrapeUrl('https://booking.com/hotel/amsterdam?aid=456', 2);

    expect($job1->uniqueId())->toBe($job2->uniqueId());
});

test('job timeout is sufficient for full cascade', function () {
    $job = new ScrapeUrl('https://example.com', 1);

    expect($job->timeout)->toBeGreaterThanOrEqual(240);
});
