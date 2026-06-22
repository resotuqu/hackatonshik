<?php

namespace App\Filament\Resources\AvatarPresetPacks\Pages;

use App\Filament\Resources\AvatarPresetPacks\AvatarPresetPackResource;
use App\Models\AvatarPreset;
use App\Support\PresetAvatar;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditAvatarPresetPack extends EditRecord
{
    protected static string $resource = AvatarPresetPackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (): void {
                    $pack = $this->record;

                    foreach (AvatarPreset::query()->where('avatar_preset_pack_id', $pack->id)->get() as $preset) {
                        if (PresetAvatar::isPathUnderPack($preset->storage_path, $pack->slug)) {
                            Storage::disk('public')->delete($preset->storage_path);
                        }
                    }

                    Storage::disk('public')->deleteDirectory(PresetAvatar::packStorageDirectory($pack->slug));
                }),
        ];
    }
}
