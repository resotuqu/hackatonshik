<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListAnalyticsEvent extends Model
{
    use HasFactory;

    protected $table = 'list_interactions';

    protected $fillable = [
        'user_id',
        'list_key',
        'event_name',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
