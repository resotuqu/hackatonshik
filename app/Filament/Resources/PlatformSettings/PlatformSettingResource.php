<?php

namespace App\Filament\Resources\PlatformSettings;

use App\Filament\Concerns\RequiresAdmin;
use App\Filament\Resources\PlatformSettings\Pages\EditPlatformSetting;
use App\Filament\Resources\PlatformSettings\Pages\ListPlatformSettings;
use App\Filament\Resources\PlatformSettings\Schemas\PlatformSettingForm;
use App\Filament\Resources\PlatformSettings\Tables\PlatformSettingsTable;
use App\Models\PlatformSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlatformSettingResource extends Resource
{
    use RequiresAdmin;

    protected static ?string $model = PlatformSetting::class;

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?string $recordRouteKeyName = 'key';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'Настройки платформы';

    protected static ?string $modelLabel = 'Настройка';

    protected static ?string $pluralModelLabel = 'Настройки';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string
    {
        return 'Контент';
    }

    public static function form(Schema $schema): Schema
    {
        return PlatformSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlatformSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlatformSettings::route('/'),
            'edit' => EditPlatformSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
