<?php

namespace App\Filament\Resources\Hackatons;

use App\Filament\Concerns\RequiresAdmin;
use App\Filament\Resources\Hackatons\Pages\CreateHackaton;
use App\Filament\Resources\Hackatons\Pages\EditHackaton;
use App\Filament\Resources\Hackatons\Pages\ListHackatons;
use App\Filament\Resources\Hackatons\Schemas\HackatonForm;
use App\Filament\Resources\Hackatons\Tables\HackatonsTable;
use App\Models\Hackaton;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HackatonResource extends Resource
{
    use RequiresAdmin;

    protected static ?string $model = Hackaton::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $navigationLabel = 'Хакатоны';

    protected static ?string $modelLabel = 'Хакатон';

    protected static ?string $pluralModelLabel = 'Хакатоны';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return 'Управление';
    }

    public static function form(Schema $schema): Schema
    {
        return HackatonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HackatonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHackatons::route('/'),
            'create' => CreateHackaton::route('/create'),
            'edit' => EditHackaton::route('/{record}/edit'),
        ];
    }
}
