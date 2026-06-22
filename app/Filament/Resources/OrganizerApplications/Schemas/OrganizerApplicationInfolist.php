<?php

namespace App\Filament\Resources\OrganizerApplications\Schemas;

use App\Enums\OrganizerApplicationStatus;
use App\Enums\OrganizerEntityType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class OrganizerApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Заявка')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.fio')
                            ->label('ФИО'),

                        TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),

                        TextEntry::make('entity_type')
                            ->label('Тип')
                            ->formatStateUsing(fn (OrganizerEntityType $state) => $state->label()),

                        TextEntry::make('company_name')
                            ->label('Компания')
                            ->placeholder('—'),

                        TextEntry::make('status')
                            ->label('Статус')
                            ->badge()
                            ->formatStateUsing(fn (OrganizerApplicationStatus $state) => $state->label()),

                        TextEntry::make('created_at')
                            ->label('Подана')
                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d.m.Y H:i') : null),

                        TextEntry::make('note')
                            ->label('Примечание')
                            ->columnSpanFull(),

                        TextEntry::make('admin_note')
                            ->label('Комментарий администратора')
                            ->columnSpanFull()
                            ->visible(fn ($record) => filled($record->admin_note)),

                        TextEntry::make('reviewer.fio')
                            ->label('Рассмотрел')
                            ->visible(fn ($record) => $record->reviewed_by !== null),

                        TextEntry::make('reviewed_at')
                            ->label('Дата решения')
                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d.m.Y H:i') : null)
                            ->visible(fn ($record) => $record->reviewed_at !== null),
                    ]),
            ]);
    }
}
