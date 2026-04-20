<?php

namespace App\Models;

use Database\Factories\ScrapeResultFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property ?string $url
 * @property string $url_hash
 * @property string $status
 * @property ?string $html_response
 * @property ?string $scrapingbee_response
 * @property bool $used_scrapingbee
 * @property bool $used_premium_proxy
 * @property ?string $title
 * @property ?string $description
 * @property ?string $image_url
 * @property ?string $error_message
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class ScrapeResult extends Model
{
    /** @use HasFactory<ScrapeResultFactory> */
    use HasFactory;

    protected $fillable = [
        'url',
        'url_hash',
        'status',
        'html_response',
        'scrapingbee_response',
        'used_scrapingbee',
        'used_premium_proxy',
        'title',
        'description',
        'image_url',
        'error_message',
    ];

    protected $attributes = [
        'used_scrapingbee' => false,
        'used_premium_proxy' => false,
    ];

    protected function casts(): array
    {
        return [
            'used_scrapingbee' => 'boolean',
            'used_premium_proxy' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ScrapeResult $model) {
            $model->url_hash ??= self::hashUrl($model->url);
        });
    }

    /**
     * Generate a hash for the URL, stripping query parameters for deduplication.
     */
    public static function hashUrl(string $url): string
    {
        $parsed = parse_url($url);
        $normalized = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '').($parsed['path'] ?? '/');

        return hash('sha256', $normalized);
    }

    /**
     * @return array{title: ?string, description: ?string, image_url: ?string}
     */
    public function toMetadata(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url,
        ];
    }
}
