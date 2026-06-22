<?php

namespace App\Actions\AvatarPreset;

use App\Models\AvatarPreset;
use App\Support\PresetAvatar;
use Illuminate\Support\Facades\Storage;

final class DeleteAvatarPresetImage
{
    public function __invoke(AvatarPreset $preset): void
    {
        $pack = $preset->pack;

        if ($pack !== null && PresetAvatar::isPathUnderPack($preset->storage_path, $pack->slug)) {
            Storage::disk('public')->delete($preset->storage_path);
        }

        $preset->delete();
    }
}
