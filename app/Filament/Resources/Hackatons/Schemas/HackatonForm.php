<?php

namespace App\Filament\Resources\Hackatons\Schemas;

use App\Enums\HackatonLevel;
use App\Enums\HackatonStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HackatonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Статус')
                            ->options(collect(HackatonStatus::cases())->mapWithKeys(
                                fn (HackatonStatus $s) => [$s->value => $s->label()]
                            ))
                            ->required(),

                        Select::make('level')
                            ->label('Уровень')
                            ->options(collect(HackatonLevel::cases())->mapWithKeys(
                                fn (HackatonLevel $l) => [$l->value => $l->label()]
                            ))
                            ->nullable(),

                        Toggle::make('is_public')
                            ->label('Публичный'),
                    ]),

                Section::make('Даты')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('start_at')
                            ->label('Начало')
                            ->required(),

                        DateTimePicker::make('end_at')
                            ->label('Конец')
                            ->required(),

                        DateTimePicker::make('registration_deadline_at')
                            ->label('Дедлайн регистрации'),
                    ]),

                Section::make('Призы')
                    ->columns(2)
                    ->schema([
                        TextInput::make('prize_fund')
                            ->label('Призовой фонд')
                            ->numeric()
                            ->prefix('₽'),

                        TextInput::make('prize_places_count')
                            ->label('Призовых мест')
                            ->numeric(),
                    ]),
            ]);
    }
}
