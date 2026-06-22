<?php

namespace App\Filament\Resources\OrganizerApplications\Tables;

use App\Enums\OrganizerApplicationStatus;
use App\Enums\OrganizerEntityType;
use App\Models\OrganizerApplication;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrganizerApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.fio')
                    ->label('Пользователь')
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('entity_type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (OrganizerEntityType $state) => $state->label()),

                TextColumn::make('company_name')
                    ->label('Компания')
                    ->placeholder('—')
                    ->limit(30),

                TextColumn::make('note')
                    ->label('Примечание')
                    ->limit(50)
                    ->tooltip(fn (OrganizerApplication $record) => $record->note),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (OrganizerApplicationStatus $state) => $state->label())
                    ->color(fn (OrganizerApplicationStatus $state) => match ($state) {
                        OrganizerApplicationStatus::Pending => Color::Amber,
                        OrganizerApplicationStatus::Approved => Color::Green,
                        OrganizerApplicationStatus::Rejected => Color::Red,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Подана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(collect(OrganizerApplicationStatus::cases())->mapWithKeys(
                        fn (OrganizerApplicationStatus $status) => [$status->value => $status->label()]
                    ))
                    ->default(OrganizerApplicationStatus::Pending->value),
            ])
            ->recordActions([
                ViewAction::make()->label('Открыть'),

                Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check')
                    ->color(Color::Green)
                    ->requiresConfirmation()
                    ->visible(fn (OrganizerApplication $record) => $record->isPending())
                    ->action(function (OrganizerApplication $record): void {
                        /** @var User $reviewer */
                        $reviewer = auth()->user();
                        abort_unless($reviewer !== null && $reviewer->can('approve', $record), 403);
                        $record->approve($reviewer);
                    }),

                Action::make('reject')
                    ->label('Отклонить')
                    ->icon('heroicon-o-x-mark')
                    ->color(Color::Red)
                    ->visible(fn (OrganizerApplication $record) => $record->isPending())
                    ->schema([
                        Textarea::make('admin_note')
                            ->label('Причина отклонения')
                            ->rows(4)
                            ->maxLength(2000),
                    ])
                    ->action(function (OrganizerApplication $record, array $data): void {
                        /** @var User $reviewer */
                        $reviewer = auth()->user();
                        abort_unless($reviewer !== null && $reviewer->can('reject', $record), 403);
                        $record->reject($reviewer, $data['admin_note'] ?? null);
                    }),
            ]);
    }
}
