<?php

namespace App\Filament\Resources\Teams\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('user.fio')
                    ->label('Капитан')
                    ->searchable(),

                TextColumn::make('hackaton.title')
                    ->label('Хакатон')
                    ->searchable()
                    ->limit(40),

                IconColumn::make('is_public')
                    ->label('Публичная')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hackaton')
                    ->label('Хакатон')
                    ->relationship('hackaton', 'title'),
            ])
            ->recordActions([
                EditAction::make()->label('Редактировать'),
            ])
            ->toolbarActions([]);
    }
}
