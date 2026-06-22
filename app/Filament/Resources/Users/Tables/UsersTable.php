<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class UsersTable
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

                TextColumn::make('fio')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nickname')
                    ->label('Никнейм')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('role')
                    ->label('Роль')
                    ->badge()
                    ->formatStateUsing(fn (UserRole $state) => $state->label())
                    ->color(fn (UserRole $state) => match ($state) {
                        UserRole::ADMIN => Color::Red,
                        UserRole::MODERATOR => Color::Orange,
                        UserRole::PARTNER => Color::Blue,
                        UserRole::JUDGE => Color::Purple,
                        UserRole::USER => Color::Gray,
                    })
                    ->sortable(),

                TextColumn::make('suspended_at')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Заблокирован' : 'Активен')
                    ->color(fn ($state) => $state ? Color::Red : Color::Green)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Зарегистрирован')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Роль')
                    ->options(collect(UserRole::cases())->mapWithKeys(
                        fn (UserRole $role) => [$role->value => $role->label()]
                    )),

                TernaryFilter::make('suspended_at')
                    ->label('Заблокированные')
                    ->nullable()
                    ->trueLabel('Только заблокированные')
                    ->falseLabel('Только активные'),
            ])
            ->recordActions([
                EditAction::make()->label('Редактировать'),

                Action::make('suspend')
                    ->label(fn ($record) => $record->suspended_at ? 'Разблокировать' : 'Заблокировать')
                    ->color(fn ($record) => $record->suspended_at ? Color::Green : Color::Red)
                    ->icon(fn ($record) => $record->suspended_at ? 'heroicon-o-lock-open' : 'heroicon-o-no-symbol')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->forceFill([
                            'suspended_at' => $record->suspended_at ? null : now(),
                        ])->save();

                        activity('user')
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperty(
                                'suspended_at',
                                $record->suspended_at !== null
                                    ? Carbon::parse($record->suspended_at)->toIso8601String()
                                    : null
                            )
                            ->log('suspension_changed');
                    }),
            ])
            ->toolbarActions([]);
    }
}
