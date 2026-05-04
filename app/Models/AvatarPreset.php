<?php

namespace App\Models;

use Database\Factories\AvatarPresetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvatarPreset extends Model
{
    /** @use HasFactory<AvatarPresetFactory> */
    use HasFactory;

    protected $fillable = [
        'avatar_preset_pack_id',
        'storage_path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(AvatarPresetPack::class, 'avatar_preset_pack_id');
    }
}
