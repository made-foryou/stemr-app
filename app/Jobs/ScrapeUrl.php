<?php

namespace App\Jobs;

use App\Models\ScrapeResult;
use App\Services\UrlScraper;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScrapeUrl implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(
        public string $url,
        public int $scrapeResultId,
    ) {}

    public function handle(UrlScraper $scraper): void
    {
        $result = ScrapeResult::find($this->scrapeResultId);

        if (! $result || $result->status !== 'pending') {
            return;
        }

        $existing = $scraper->findOrCreate($this->url);

        if ($existing) {
            $result->update([
                'status' => 'completed',
                'title' => $existing->title,
                'description' => $existing->description,
                'image_url' => $existing->image_url,
            ]);

            return;
        }

        try {
            $scraper->scrapeAndUpdate($result);
        } catch (\Throwable $e) {
            if ($result->status === 'pending') {
                $result->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }

    public function uniqueId(): string
    {
        return ScrapeResult::hashUrl($this->url);
    }
}
