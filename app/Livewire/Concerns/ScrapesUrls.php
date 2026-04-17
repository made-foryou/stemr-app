<?php

namespace App\Livewire\Concerns;

use App\Jobs\ScrapeUrl;
use App\Models\ScrapeResult;
use App\Services\UrlScraper;
use Illuminate\Support\Js;

trait ScrapesUrls
{
    public ?int $scrapeResultId = null;

    /** @var array{title?: ?string, description?: ?string, image_url?: ?string} */
    public array $scrapedData = [];

    /**
     * @return array<string, string> Mapping van scraped keys naar component properties.
     */
    abstract protected function getScrapedFieldMapping(): array;

    public function dispatchScrapeJob(string $url): void
    {
        $this->fetchError = '';
        $this->scrapedData = [];

        $existing = app(UrlScraper::class)->findOrCreate($url);

        if ($existing) {
            $this->handleScrapedResult($existing->toMetadata());

            return;
        }

        $result = ScrapeResult::create([
            'url' => $url,
            'status' => 'pending',
        ]);

        ScrapeUrl::dispatch($url, $result->id);

        $this->scrapeResultId = $result->id;
        $this->isFetchingUrl = true;
    }

    public function checkScrapeResult(): void
    {
        if (! $this->isFetchingUrl || ! $this->scrapeResultId) {
            return;
        }

        $result = ScrapeResult::find($this->scrapeResultId);

        if (! $result) {
            $this->isFetchingUrl = false;
            $this->scrapeResultId = null;

            return;
        }

        if ($result->status === 'completed') {
            $this->isFetchingUrl = false;
            $this->scrapeResultId = null;
            $this->handleScrapedResult($result->toMetadata());
        } elseif ($result->status === 'failed') {
            $this->fetchError = $result->error_message ?? 'Er is een fout opgetreden bij het ophalen van de URL.';
            $this->isFetchingUrl = false;
            $this->scrapeResultId = null;
        }
    }

    public function applySuggestion(string $field): void
    {
        $mapping = $this->getScrapedFieldMapping();

        if (! isset($mapping[$field]) || empty($this->scrapedData[$field])) {
            return;
        }

        $property = $mapping[$field];
        $value = $this->scrapedData[$field];

        $maxLengths = ['title' => 100, 'description' => 500];
        if (isset($maxLengths[$field])) {
            $value = mb_substr($value, 0, $maxLengths[$field]);
        }

        $this->{$property} = $value;
        $this->js('$wire.'.$property.' = '.Js::from($value));

        unset($this->scrapedData[$field]);
    }

    public function applyAllSuggestions(): void
    {
        $mapping = $this->getScrapedFieldMapping();

        foreach (array_keys($mapping) as $field) {
            if (! empty($this->scrapedData[$field])) {
                $this->applySuggestion($field);
            }
        }
    }

    public function dismissSuggestion(string $field): void
    {
        unset($this->scrapedData[$field]);
    }

    public function dismissAllSuggestions(): void
    {
        $this->scrapedData = [];
    }

    /**
     * @param  array{title: ?string, description: ?string, image_url: ?string}  $data
     */
    private function handleScrapedResult(array $data): void
    {
        $mapping = $this->getScrapedFieldMapping();
        $allEmpty = true;

        foreach ($mapping as $scrapedKey => $property) {
            if (! empty($this->{$property})) {
                $allEmpty = false;
                break;
            }
        }

        if ($allEmpty) {
            $this->scrapedData = $data;
            $this->applyAllSuggestions();

            return;
        }

        $this->scrapedData = array_filter($data, fn ($value) => ! empty($value));
    }
}
