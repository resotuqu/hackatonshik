<?php

namespace App\Filament\Resources\PlatformSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlatformSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Функция')
                    ->searchable()
                    ->description(fn ($record) => $record->description),

                TextColumn::make('key')
                    ->label('Ключ')
                    ->fontFamily('mono')
                    ->color(Color::Gray)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('value')
                    ->label('Статус')
                    ->boolean()
                    ->getStateUsing(fn ($record) => (bool) $record->value),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('Изменить'),
            ]);
    }
}
