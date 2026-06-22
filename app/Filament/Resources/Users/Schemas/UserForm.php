<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')
                    ->columns(2)
                    ->schema([
                        TextInput::make('fio')
                            ->label('ФИО')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('nickname')
                            ->label('Никнейм')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(20),

                        DatePicker::make('date_of_birth')
                            ->label('Дата рождения'),

                        Select::make('role')
                            ->label('Роль')
                            ->options(collect(UserRole::cases())->mapWithKeys(
                                fn (UserRole $role) => [$role->value => $role->label()]
                            ))
                            ->default(UserRole::USER->value)
                            ->required(),
                    ]),

                Section::make('Пароль')
                    ->schema([
                        TextInput::make('password')
                            ->label('Новый пароль')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->minLength(8)
                            ->helperText('Оставьте пустым, чтобы не менять'),
                    ]),

                Section::make('Описание')
                    ->schema([
                        Textarea::make('description')
                            ->label('О себе')
                            ->rows(4)
                            ->maxLength(2000),
                    ]),
            ]);
    }
}
