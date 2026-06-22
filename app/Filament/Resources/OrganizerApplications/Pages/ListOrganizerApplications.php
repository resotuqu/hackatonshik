<?php

namespace App\Filament\Resources\OrganizerApplications\Pages;

use App\Filament\Resources\OrganizerApplications\OrganizerApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListOrganizerApplications extends ListRecords
{
    protected static string $resource = OrganizerApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
