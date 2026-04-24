<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackatonAnnouncementImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackaton_announcement_id',
        'path',
        'sort_order',
        'alt',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(HackatonAnnouncement::class, 'hackaton_announcement_id');
    }
}
