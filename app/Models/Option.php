<?php

namespace App\Models;

use Database\Factories\OptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['name', 'image_url', 'description', 'sort_order'])]
#[Hidden(['id', 'poll_id'])]
class Option extends Model
{
    /** @use HasFactory<OptionFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Option $option): void {
            $option->uuid ??= Str::uuid()->toString();
        });
    }

    /**
     * Get the route key name for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the poll that this option belongs to.
     *
     * @return BelongsTo<Poll, $this>
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }
}
