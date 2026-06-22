<?php

namespace App\Filament\Resources\ActivityLog\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogTable
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

                TextColumn::make('log_name')
                    ->label('Лог')
                    ->badge()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Действие')
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('Объект')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—')
                    ->badge(),

                TextColumn::make('causer.fio')
                    ->label('Пользователь')
                    ->searchable()
                    ->default('—'),

                TextColumn::make('created_at')
                    ->label('Время')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Тип лога')
                    ->options([
                        'user' => 'Пользователи',
                        'hackaton' => 'Хакатоны',
                        'team' => 'Команды',
                        'default' => 'Общий',
                    ]),
            ])
            ->toolbarActions([]);
    }
}
