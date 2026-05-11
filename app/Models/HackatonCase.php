<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class HackatonCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackaton_id',
        'title',
        'description',
        'resources_json',
        'rubric_json',
        'sort_order',
        'is_published',
        'publish_at',
        'deadline_at',
    ];

    protected $attributes = [
        'sort_order' => 0,
        'is_published' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'publish_at' => 'datetime',
            'deadline_at' => 'datetime',
            'resources_json' => 'array',
            'rubric_json' => 'array',
        ];
    }

    public function hackaton(): BelongsTo
    {
        return $this->belongsTo(Hackaton::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(HackatonCaseField::class)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(HackatonCaseImage::class)->orderBy('sort_order');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(HackatonCaseSubmission::class);
    }

    public function isOpenForSubmission(): bool
    {
        if (! $this->isPublishedNow()) {
            return false;
        }

        if ($this->deadline_at === null || $this->deadline_at === '') {
            return true;
        }

        return Carbon::parse((string) $this->deadline_at)->isFuture();
    }

    public function isPublishedNow(): bool
    {
        if (! $this->is_published) {
            return false;
        }

        if ($this->publish_at === null || $this->publish_at === '') {
            return true;
        }

        return Carbon::parse((string) $this->publish_at)->isPast();
    }
}
