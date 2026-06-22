<?php

namespace App\Actions\AvatarPreset;

use App\Models\AvatarPreset;
use App\Models\AvatarPresetPack;
use App\Support\PresetAvatar;
use Illuminate\Support\Facades\Storage;

final class StoreAvatarPresetImages
{
    /**
     * @param  array<int, string>  $storagePaths
     */
    public function __invoke(AvatarPresetPack $pack, array $storagePaths): int
    {
        Storage::disk('public')->makeDirectory(PresetAvatar::packStorageDirectory($pack->slug));

        $nextOrder = (int) (AvatarPreset::query()
            ->where('avatar_preset_pack_id', $pack->id)
            ->max('sort_order') ?? -1) + 1;

        $created = 0;

        foreach ($storagePaths as $path) {
            if (! is_string($path) || $path === '') {
                continue;
            }

            if (! PresetAvatar::isPathUnderPack($path, $pack->slug)) {
                continue;
            }

            if (! Storage::disk('public')->exists($path)) {
                continue;
            }

            AvatarPreset::query()->create([
                'avatar_preset_pack_id' => $pack->id,
                'storage_path' => $path,
                'sort_order' => $nextOrder,
            ]);

            $nextOrder++;
            $created++;
        }

        return $created;
    }
}
