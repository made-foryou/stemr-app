<?php

use App\Models\ScrapeResult;
use App\Services\UrlScraper;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['services.scrapingbee.api_key' => null]);
    config(['services.scrapingbee.refresh_days' => 30]);
});

// --- Metadata extraction ---

test('scrapes open graph title, description and image via HTTP GET', function () {
    Http::fake([
        'https://example.com/hotel' => Http::response('<html><head>
            <meta property="og:title" content="Hotel Amsterdam" />
            <meta property="og:description" content="Mooi hotel in het centrum" />
            <meta property="og:image" content="https://example.com/photo.jpg" />
        </head><body></body></html>'),
    ]);

    $scraper = app(UrlScraper::class);
    $result = ScrapeResult::create(['url' => 'https://example.com/hotel', 'status' => 'pending']);
    $result = $scraper->scrapeAndUpdate($result);

    expect($result)
        ->toBeInstanceOf(ScrapeResult::class)
        ->title->toBe('Hotel Amsterdam')
        ->description->toBe('Mooi hotel in het centrum')
        ->image_url->toBe('https://example.com/photo.jpg')
        ->status->toBe('completed')
        ->used_scrapingbee->toBeFalse();
});

test('stores raw html response in database', function () {
    Http::fake([
        'https://example.com/raw' => Http::response('<html><head>
            <meta property="og:title" content="Test" />
        </head><body>Content here</body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/raw', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->html_response)->toContain('Content here');
});

test('falls back to title tag when og:title is missing', function () {
    Http::fake([
        'https://example.com/page' => Http::response('<html><head>
            <title>Pagina Titel</title>
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/page', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->title)->toBe('Pagina Titel');
});

test('falls back to meta description when og:description is missing', function () {
    Http::fake([
        'https://example.com/page-desc' => Http::response('<html><head>
            <title>Test pagina</title>
            <meta name="description" content="Een meta beschrijving" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/page-desc', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->description)->toBe('Een meta beschrijving');
});

test('resolves relative image urls', function () {
    Http::fake([
        'https://example.com/page-rel' => Http::response('<html><head>
            <title>Test</title>
            <meta property="og:image" content="/images/photo.jpg" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/page-rel', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->image_url)->toBe('https://example.com/images/photo.jpg');
});

test('resolves protocol-relative image urls', function () {
    Http::fake([
        'https://example.com/page-proto' => Http::response('<html><head>
            <title>Test</title>
            <meta property="og:image" content="//cdn.example.com/photo.jpg" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/page-proto', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->image_url)->toBe('https://cdn.example.com/photo.jpg');
});

test('resolves relative image urls without leading slash', function () {
    Http::fake([
        'https://example.com/page-noslash' => Http::response('<html><head>
            <title>Test</title>
            <meta property="og:image" content="images/photo.jpg" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/page-noslash', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->image_url)->toBe('https://example.com/images/photo.jpg');
});

test('handles content attribute before property attribute in og tags', function () {
    Http::fake([
        'https://example.com/reversed' => Http::response('<html><head>
            <meta content="Reversed Title" property="og:title" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/reversed', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->title)->toBe('Reversed Title');
});

test('handles content attribute before name attribute in meta description', function () {
    Http::fake([
        'https://example.com/reversed-desc' => Http::response('<html><head>
            <title>Test</title>
            <meta content="Reversed description" name="description" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/reversed-desc', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->description)->toBe('Reversed description');
});

test('decodes html entities in scraped values', function () {
    Http::fake([
        'https://example.com/entities' => Http::response('<html><head>
            <meta property="og:title" content="Hotel &amp; Spa &quot;De Luxe&quot;" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/entities', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->title)->toBe('Hotel & Spa "De Luxe"');
});

// --- Scrape cascade ---

test('falls back to ScrapingBee Classic when HTTP GET finds no metadata', function () {
    config(['services.scrapingbee.api_key' => 'test-key']);

    Http::fake([
        'https://example.com/protected' => Http::response('<html><head></head><body>Bot challenge</body></html>'),
        'https://app.scrapingbee.com/api/v1*' => Http::response('<html><head>
            <meta property="og:title" content="ScrapingBee Title" />
            <meta property="og:description" content="Via ScrapingBee" />
        </head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/protected', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result)
        ->title->toBe('ScrapingBee Title')
        ->used_scrapingbee->toBeTrue()
        ->used_premium_proxy->toBeFalse()
        ->status->toBe('completed');

    Http::assertSentCount(2);
});

test('falls back to ScrapingBee Premium when Classic returns 500', function () {
    config(['services.scrapingbee.api_key' => 'test-key']);

    Http::fake([
        'https://example.com/booking' => Http::response('<html><head></head><body></body></html>'),
        'https://app.scrapingbee.com/api/v1*' => Http::sequence()
            ->push('Blocked', 500)
            ->push('<html><head><meta property="og:title" content="Premium Title" /></head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/booking', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result)
        ->title->toBe('Premium Title')
        ->used_scrapingbee->toBeTrue()
        ->used_premium_proxy->toBeTrue()
        ->status->toBe('completed');

    Http::assertSentCount(3);
});

test('marks as failed when all methods fail', function () {
    config(['services.scrapingbee.api_key' => 'test-key']);

    Http::fake([
        'https://example.com/hopeless' => Http::response('<html><head></head><body></body></html>'),
        'https://app.scrapingbee.com/api/v1*' => Http::sequence()
            ->push('Blocked', 500)
            ->push('<html><head></head><body>Still nothing</body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/hopeless', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result)
        ->status->toBe('failed')
        ->error_message->toContain('Geen metadata gevonden');
});

test('marks as failed when HTTP GET fails and ScrapingBee is not configured', function () {
    Http::fake([
        'https://example.com/no-key' => Http::response('<html><head></head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/no-key', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result)->status->toBe('failed');
});

test('marks as failed when HTTP GET returns error and ScrapingBee not configured', function () {
    Http::fake([
        'https://example.com/error' => Http::response('Not Found', 404),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/error', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result)->status->toBe('failed');
});

test('handles ScrapingBee api failure gracefully', function () {
    config(['services.scrapingbee.api_key' => 'test-key']);

    Http::fake([
        'https://example.com/bee-fail' => Http::response('<html><head></head><body></body></html>'),
        'https://app.scrapingbee.com/api/v1*' => Http::response('Service Unavailable', 503),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/bee-fail', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result)->status->toBe('failed');
});

// --- URL hash / caching ---

test('reuses recent completed result from database', function () {
    Http::fake();

    ScrapeResult::factory()->create([
        'url' => 'https://example.com/cached',
        'status' => 'completed',
        'title' => 'Cached Title',
        'created_at' => now()->subDays(10),
    ]);

    $existing = app(UrlScraper::class)->findOrCreate('https://example.com/cached');

    expect($existing)->not->toBeNull();
    expect($existing->title)->toBe('Cached Title');
    Http::assertNothingSent();
});

test('re-scrapes when existing result is expired', function () {
    ScrapeResult::factory()->create([
        'url' => 'https://example.com/expired',
        'status' => 'completed',
        'title' => 'Old Title',
        'created_at' => now()->subDays(31),
    ]);

    $existing = app(UrlScraper::class)->findOrCreate('https://example.com/expired');

    expect($existing)->toBeNull();
});

test('does not reuse failed results', function () {
    ScrapeResult::factory()->failed()->create([
        'url' => 'https://example.com/retry',
        'created_at' => now()->subMinutes(5),
    ]);

    $existing = app(UrlScraper::class)->findOrCreate('https://example.com/retry');

    expect($existing)->toBeNull();
});

test('URL hash ignores query parameters', function () {
    $hash1 = ScrapeResult::hashUrl('https://booking.com/hotel/amsterdam?aid=123&lang=nl');
    $hash2 = ScrapeResult::hashUrl('https://booking.com/hotel/amsterdam?aid=456&currency=eur');
    $hash3 = ScrapeResult::hashUrl('https://booking.com/hotel/amsterdam');

    expect($hash1)->toBe($hash2)->toBe($hash3);
});

test('URL hash differentiates different paths', function () {
    $hash1 = ScrapeResult::hashUrl('https://booking.com/hotel/amsterdam');
    $hash2 = ScrapeResult::hashUrl('https://booking.com/hotel/rotterdam');

    expect($hash1)->not->toBe($hash2);
});

test('matches URL with query params to cached result without query params', function () {
    Http::fake();

    ScrapeResult::factory()->create([
        'url' => 'https://booking.com/hotel/amsterdam',
        'status' => 'completed',
        'title' => 'Hotel Amsterdam',
        'created_at' => now()->subDays(5),
    ]);

    $existing = app(UrlScraper::class)->findOrCreate('https://booking.com/hotel/amsterdam?aid=123&lang=nl');

    expect($existing)->not->toBeNull();
    expect($existing->title)->toBe('Hotel Amsterdam');
});

// --- No pending records left ---

test('result is never left as pending after scrapeAndUpdate', function () {
    Http::fake([
        'https://example.com/always-resolves' => Http::response('<html><head></head><body></body></html>'),
    ]);

    $result = ScrapeResult::create(['url' => 'https://example.com/always-resolves', 'status' => 'pending']);
    app(UrlScraper::class)->scrapeAndUpdate($result);

    expect($result->status)->not->toBe('pending');
});
