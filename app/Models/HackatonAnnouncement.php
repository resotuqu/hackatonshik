<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HackatonAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackaton_id',
        'created_by',
        'title',
        'body',
        'is_draft',
        'template_key',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_draft' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function hackaton(): BelongsTo
    {
        return $this->belongsTo(Hackaton::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(HackatonAnnouncementImage::class)->orderBy('sort_order');
    }
}
