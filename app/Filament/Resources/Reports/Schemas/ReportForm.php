<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Enums\ReportStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Жалоба')
                    ->schema([
                        Select::make('status')
                            ->label('Статус')
                            ->options(collect(ReportStatus::cases())->mapWithKeys(
                                fn (ReportStatus $s) => [$s->value => $s->label()]
                            ))
                            ->required(),

                        Textarea::make('moderator_note')
                            ->label('Заметка модератора')
                            ->rows(3)
                            ->helperText('Видна только персоналу платформы'),
                    ]),
            ]);
    }
}
