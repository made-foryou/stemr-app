<?php

namespace App\Services;

use App\Models\ScrapeResult;
use Illuminate\Support\Facades\Http;

class UrlScraper
{
    /**
     * Find a recent completed result or create a pending one for the given URL.
     *
     * Uses the URL hash (without query parameters) for deduplication.
     */
    public function findOrCreate(string $url): ?ScrapeResult
    {
        $refreshDays = config('services.scrapingbee.refresh_days', 30);

        $existing = ScrapeResult::query()
            ->where('url_hash', ScrapeResult::hashUrl($url))
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($refreshDays))
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        return null;
    }

    /**
     * Perform the full scrape cascade and update the ScrapeResult record.
     *
     * Order: 1) HTTP GET → 2) ScrapingBee Classic → 3) ScrapingBee Premium.
     * The record is always set to 'completed' or 'failed' — never left as 'pending'.
     */
    public function scrapeAndUpdate(ScrapeResult $result): ScrapeResult
    {
        // Step 1: Simple HTTP GET
        $html = $this->fetchHtml($result->url);

        if ($html) {
            $result->update(['html_response' => $html]);
            $data = $this->extractMetadata($html, $result->url);

            if ($data['title'] !== null) {
                return $this->markCompleted($result, $data);
            }
        }

        // Step 2: ScrapingBee with Classic proxy
        $scrapingBeeHtml = $this->fetchWithScrapingBee($result->url, premiumProxy: false);

        if ($scrapingBeeHtml) {
            $result->update([
                'scrapingbee_response' => $scrapingBeeHtml,
                'used_scrapingbee' => true,
            ]);

            $data = $this->extractMetadata($scrapingBeeHtml, $result->url);

            if ($data['title'] !== null) {
                return $this->markCompleted($result, $data);
            }
        }

        // Step 3: ScrapingBee with Premium proxy (for sites like Booking.com)
        $premiumHtml = $this->fetchWithScrapingBee($result->url, premiumProxy: true);

        if ($premiumHtml) {
            $result->update([
                'scrapingbee_response' => $premiumHtml,
                'used_scrapingbee' => true,
                'used_premium_proxy' => true,
            ]);

            $data = $this->extractMetadata($premiumHtml, $result->url);

            if ($data['title'] !== null) {
                return $this->markCompleted($result, $data);
            }
        }

        // All methods failed
        $result->update([
            'status' => 'failed',
            'error_message' => 'Geen metadata gevonden op deze pagina. De website blokkeert mogelijk geautomatiseerde verzoeken.',
        ]);

        return $result;
    }

    /**
     * @param  array{title: ?string, description: ?string, image_url: ?string}  $data
     */
    private function markCompleted(ScrapeResult $result, array $data): ScrapeResult
    {
        $result->update([
            'status' => 'completed',
            'title' => $data['title'],
            'description' => $data['description'],
            'image_url' => $data['image_url'],
        ]);

        return $result;
    }

    /**
     * Fetch HTML using a simple HTTP GET request.
     *
     * Returns null on failure instead of throwing, so the cascade can continue.
     */
    private function fetchHtml(string $url): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.9,nl;q=0.8',
                ])
                ->get($url);

            $response->throw();

            return $response->body();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Fetch HTML via ScrapingBee API with JavaScript rendering.
     *
     * Returns null if ScrapingBee is not configured, returns a 500 error, or the request fails.
     */
    private function fetchWithScrapingBee(string $url, bool $premiumProxy = false): ?string
    {
        $apiKey = config('services.scrapingbee.api_key');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $params = [
                'api_key' => $apiKey,
                'url' => $url,
                'render_js' => 'true',
            ];

            if ($premiumProxy) {
                $params['premium_proxy'] = 'true';
            }

            $response = Http::timeout(120)
                ->get('https://app.scrapingbee.com/api/v1', $params);

            $response->throw();

            return $response->body();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Extract metadata from HTML using Open Graph and meta tags.
     *
     * @return array{title: ?string, description: ?string, image_url: ?string}
     */
    private function extractMetadata(string $html, string $url): array
    {
        return [
            'title' => $this->extractOgTag($html, 'og:title') ?? $this->extractTitle($html),
            'description' => $this->extractOgTag($html, 'og:description') ?? $this->extractMetaDescription($html),
            'image_url' => $this->resolveUrl($this->extractOgTag($html, 'og:image'), $url),
        ];
    }

    private function extractOgTag(string $html, string $property): ?string
    {
        if (preg_match('/<meta[^>]+property=["\']'.preg_quote($property, '/').'["\'][^>]+content=["\']([^"\']+)["\'][^>]*\/?>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        if (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']'.preg_quote($property, '/').'["\'][^>]*\/?>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
            return html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    private function extractMetaDescription(string $html): ?string
    {
        if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)["\'][^>]*\/?>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        if (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+name=["\']description["\'][^>]*\/?>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    private function resolveUrl(?string $imageUrl, string $baseUrl): ?string
    {
        if ($imageUrl === null) {
            return null;
        }

        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }

        $parsed = parse_url($baseUrl);
        $base = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '');

        if (str_starts_with($imageUrl, '//')) {
            return ($parsed['scheme'] ?? 'https').':'.$imageUrl;
        }

        if (str_starts_with($imageUrl, '/')) {
            return $base.$imageUrl;
        }

        return $base.'/'.$imageUrl;
    }
}
