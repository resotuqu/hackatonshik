<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\TeamMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('reportable_type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match (class_basename($state)) {
                        'Hackaton' => 'Хакатон',
                        'Team' => 'Команда',
                        'TeamMessage' => 'Сообщение',
                        default => class_basename($state),
                    })
                    ->color(fn (string $state) => match (class_basename($state)) {
                        'Hackaton' => Color::Blue,
                        'Team' => Color::Green,
                        'TeamMessage' => Color::Orange,
                        default => Color::Gray,
                    }),

                TextColumn::make('reporter.fio')
                    ->label('Отправитель')
                    ->searchable(),

                TextColumn::make('reason')
                    ->label('Причина')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->reason),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (ReportStatus $state) => $state->label())
                    ->color(fn (ReportStatus $state) => match ($state) {
                        ReportStatus::Pending => Color::Amber,
                        ReportStatus::Reviewed => Color::Green,
                        ReportStatus::Dismissed => Color::Gray,
                    })
                    ->sortable(),

                TextColumn::make('reviewer.fio')
                    ->label('Рассмотрел')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(collect(ReportStatus::cases())->mapWithKeys(
                        fn (ReportStatus $s) => [$s->value => $s->label()]
                    ))
                    ->default(ReportStatus::Pending->value),

                SelectFilter::make('reportable_type')
                    ->label('Тип объекта')
                    ->options([
                        'App\\Models\\Hackaton' => 'Хакатон',
                        'App\\Models\\Team' => 'Команда',
                        'App\\Models\\TeamMessage' => 'Сообщение чата',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('Рассмотреть'),

                Action::make('dismiss')
                    ->label('Отклонить')
                    ->color(Color::Gray)
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->visible(fn (Report $record) => $record->status === ReportStatus::Pending)
                    ->action(fn (Report $record) => $record->resolve(ReportStatus::Dismissed)),

                Action::make('deleteMessage')
                    ->label('Удалить сообщение')
                    ->color(Color::Red)
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->visible(fn (Report $record) => $record->reportable_type === TeamMessage::class
                        && $record->reportable !== null)
                    ->action(function (Report $record): void {
                        $record->reportable?->delete();
                        $record->resolve(ReportStatus::Reviewed, 'Сообщение удалено модератором');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('dismiss')
                        ->label('Отклонить выбранные')
                        ->icon('heroicon-o-x-mark')
                        ->color(Color::Gray)
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(function (Report $record): void {
                                if ($record->status === ReportStatus::Pending) {
                                    $record->resolve(ReportStatus::Dismissed);
                                }
                            });
                        }),

                    BulkAction::make('review')
                        ->label('Отметить рассмотренными')
                        ->icon('heroicon-o-check')
                        ->color(Color::Green)
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(function (Report $record): void {
                                if ($record->status === ReportStatus::Pending) {
                                    $record->resolve(ReportStatus::Reviewed);
                                }
                            });
                        }),
                ]),
            ]);
    }
}
