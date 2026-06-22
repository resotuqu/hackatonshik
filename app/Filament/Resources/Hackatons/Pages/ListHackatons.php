<?php

namespace App\Filament\Resources\Hackatons\Pages;

use App\Filament\Resources\Hackatons\HackatonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHackatons extends ListRecords
{
    protected static string $resource = HackatonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
