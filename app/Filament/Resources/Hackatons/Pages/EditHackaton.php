<?php

namespace App\Filament\Resources\Hackatons\Pages;

use App\Filament\Resources\Hackatons\HackatonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHackaton extends EditRecord
{
    protected static string $resource = HackatonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
