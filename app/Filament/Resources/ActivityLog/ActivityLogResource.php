<?php

namespace App\Filament\Resources\ActivityLog;

use App\Filament\Concerns\RequiresStaff;
use App\Filament\Resources\ActivityLog\Pages\ListActivityLog;
use App\Filament\Resources\ActivityLog\Tables\ActivityLogTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    use RequiresStaff;

    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Журнал действий';

    protected static ?string $modelLabel = 'Запись';

    protected static ?string $pluralModelLabel = 'Журнал действий';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return 'Модерация';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLog::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
