<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Обращение')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Имя')
                            ->disabled(),

                        TextInput::make('email')
                            ->label('Email')
                            ->disabled(),

                        TextInput::make('telegram')
                            ->label('Telegram')
                            ->disabled(),

                        TextInput::make('subject')
                            ->label('Тема')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->label('Сообщение')
                            ->disabled()
                            ->rows(8)
                            ->columnSpanFull(),

                        TextInput::make('ip_address')
                            ->label('IP')
                            ->disabled(),

                        TextInput::make('created_at')
                            ->label('Получено')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d.m.Y H:i') : null),
                    ]),
            ]);
    }
}
