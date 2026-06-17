<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\HackatonTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HackatonTemplate extends Model
{
    /** @use HasFactory<HackatonTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'level',
        'start_offset_days',
        'end_offset_days',
        'registration_deadline_offset_days',
        'default_documents',
        'default_case',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_documents' => 'array',
            'default_case' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
