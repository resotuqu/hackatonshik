<?php

namespace App\Filament\Resources\OrganizerApplications;

use App\Filament\Concerns\RequiresStaff;
use App\Filament\Resources\OrganizerApplications\Pages\ListOrganizerApplications;
use App\Filament\Resources\OrganizerApplications\Pages\ViewOrganizerApplication;
use App\Filament\Resources\OrganizerApplications\Schemas\OrganizerApplicationInfolist;
use App\Filament\Resources\OrganizerApplications\Tables\OrganizerApplicationsTable;
use App\Models\OrganizerApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizerApplicationResource extends Resource
{
    use RequiresStaff;

    protected static ?string $model = OrganizerApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Заявки организаторов';

    protected static ?string $modelLabel = 'Заявка организатора';

    protected static ?string $pluralModelLabel = 'Заявки организаторов';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return 'Управление';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = OrganizerApplication::query()->pending()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizerApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizerApplicationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizerApplications::route('/'),
            'view' => ViewOrganizerApplication::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
