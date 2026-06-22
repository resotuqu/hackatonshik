<?php

namespace App\Filament\Resources\Hackatons\Tables;

use App\Enums\HackatonStatus;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HackatonsTable
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
                    ->label('Организатор')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (HackatonStatus $state) => $state->label())
                    ->color(fn (HackatonStatus $state) => match ($state) {
                        HackatonStatus::DRAFT => Color::Gray,
                        HackatonStatus::PUBLISHED => Color::Blue,
                        HackatonStatus::REGISTRATION_OPEN => Color::Green,
                        HackatonStatus::IN_PROGRESS => Color::Indigo,
                        HackatonStatus::JUDGING => Color::Yellow,
                        HackatonStatus::FINISHED => Color::Teal,
                        HackatonStatus::ARCHIVED => Color::Slate,
                        default => Color::Gray,
                    })
                    ->sortable(),

                IconColumn::make('is_public')
                    ->label('Публичный')
                    ->boolean(),

                TextColumn::make('start_at')
                    ->label('Начало')
                    ->dateTime('d.m.Y')
                    ->sortable(),

                TextColumn::make('end_at')
                    ->label('Конец')
                    ->dateTime('d.m.Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(collect(HackatonStatus::cases())->mapWithKeys(
                        fn (HackatonStatus $s) => [$s->value => $s->label()]
                    )),
            ])
            ->recordActions([
                EditAction::make()->label('Редактировать'),
            ])
            ->toolbarActions([]);
    }
}
