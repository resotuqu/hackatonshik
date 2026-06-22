<?php

namespace App\Filament\Resources\Reports;

use App\Filament\Concerns\RequiresStaff;
use App\Filament\Resources\Reports\Pages\EditReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Filament\Resources\Reports\Schemas\ReportForm;
use App\Filament\Resources\Reports\Tables\ReportsTable;
use App\Models\Report;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    use RequiresStaff;

    protected static ?string $model = Report::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Жалобы';

    protected static ?string $modelLabel = 'Жалоба';

    protected static ?string $pluralModelLabel = 'Жалобы';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return 'Модерация';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Report::query()->where('status', 'pending')->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return ReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
            'edit' => EditReport::route('/{record}/edit'),
        ];
    }
}
