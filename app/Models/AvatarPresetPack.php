<?php

namespace App\Models;

use Database\Factories\AvatarPresetPackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvatarPresetPack extends Model
{
    /** @use HasFactory<AvatarPresetPackFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function presets(): HasMany
    {
        return $this->hasMany(AvatarPreset::class);
    }
}
