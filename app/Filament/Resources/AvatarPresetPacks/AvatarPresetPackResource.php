<?php

namespace App\Filament\Resources\AvatarPresetPacks;

use App\Filament\Concerns\RequiresAdmin;
use App\Filament\Resources\AvatarPresetPacks\Pages\CreateAvatarPresetPack;
use App\Filament\Resources\AvatarPresetPacks\Pages\EditAvatarPresetPack;
use App\Filament\Resources\AvatarPresetPacks\Pages\ListAvatarPresetPacks;
use App\Filament\Resources\AvatarPresetPacks\RelationManagers\PresetsRelationManager;
use App\Filament\Resources\AvatarPresetPacks\Schemas\AvatarPresetPackForm;
use App\Filament\Resources\AvatarPresetPacks\Tables\AvatarPresetPacksTable;
use App\Models\AvatarPresetPack;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AvatarPresetPackResource extends Resource
{
    use RequiresAdmin;

    protected static ?string $model = AvatarPresetPack::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Аватарки';

    protected static ?string $modelLabel = 'Пак аватаров';

    protected static ?string $pluralModelLabel = 'Паки аватаров';

    protected static ?string $slug = 'avatar-presets';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string
    {
        return 'Контент';
    }

    public static function form(Schema $schema): Schema
    {
        return AvatarPresetPackForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvatarPresetPacksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PresetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAvatarPresetPacks::route('/'),
            'create' => CreateAvatarPresetPack::route('/create'),
            'edit' => EditAvatarPresetPack::route('/{record}/edit'),
        ];
    }
}
