<?php

namespace App\Filament\Resources\AvatarPresetPacks\Pages;

use App\Filament\Resources\AvatarPresetPacks\AvatarPresetPackResource;
use App\Support\PresetAvatar;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateAvatarPresetPack extends CreateRecord
{
    protected static string $resource = AvatarPresetPackResource::class;

    protected function afterCreate(): void
    {
        Storage::disk('public')->makeDirectory(
            PresetAvatar::packStorageDirectory($this->record->slug)
        );
    }
}
