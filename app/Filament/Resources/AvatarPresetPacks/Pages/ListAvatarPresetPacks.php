<?php

namespace App\Filament\Resources\AvatarPresetPacks\Pages;

use App\Filament\Resources\AvatarPresetPacks\AvatarPresetPackResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAvatarPresetPacks extends ListRecords
{
    protected static string $resource = AvatarPresetPackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
