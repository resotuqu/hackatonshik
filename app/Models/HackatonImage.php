<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackatonImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackaton_id',
        'path',
        'sort_order',
        'alt',
    ];

    public function hackaton(): BelongsTo
    {
        return $this->belongsTo(Hackaton::class);
    }
}
