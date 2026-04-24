<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HackatonCaseField extends Model
{
    use HasFactory;

    public const TYPE_TEXT = 'text';

    public const TYPE_URL = 'url';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_FILE = 'file';

    protected $fillable = [
        'hackaton_case_id',
        'label',
        'key',
        'type',
        'is_required',
        'sort_order',
        'options_json',
    ];

    protected $attributes = [
        'is_required' => false,
        'sort_order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'options_json' => 'array',
        ];
    }

    public static function allowedTypes(): array
    {
        return [
            self::TYPE_TEXT,
            self::TYPE_URL,
            self::TYPE_TEXTAREA,
            self::TYPE_FILE,
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(HackatonCase::class, 'hackaton_case_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(HackatonCaseAnswer::class);
    }
}
