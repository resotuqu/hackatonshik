<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['key', 'value', 'label', 'description'];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    public static function isEnabled(string $key): bool
    {
        return (bool) Cache::remember(
            "platform_setting:{$key}",
            now()->addMinutes(10),
            fn () => static::query()->where('key', $key)->value('value')
        );
    }

    public static function toggle(string $key): bool
    {
        $setting = static::query()->where('key', $key)->firstOrFail();
        $newValue = ! ((bool) $setting->value);
        $setting->forceFill(['value' => $newValue ? '1' : '0'])->save();
        Cache::forget("platform_setting:{$key}");

        return $newValue;
    }

    /** @return Collection<int, static> */
    public static function allSettings(): Collection
    {
        return static::query()->orderBy('key')->get();
    }
}
