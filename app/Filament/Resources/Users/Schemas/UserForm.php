<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

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
                            ->maxLength(255)
                            ->helperText('При изменении верификация email сбросится автоматически'),

                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(20)
                            ->helperText('При изменении или очистке верификация телефона сбросится автоматически'),

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

                Section::make('Верификация')
                    ->columns(2)
                    ->hiddenOn('create')
                    ->schema([
                        Placeholder::make('email_verified_at')
                            ->label('Статус email')
                            ->content(function ($record): HtmlString {
                                if ($record?->email_verified_at) {
                                    return new HtmlString(
                                        '<span class="text-success-600 dark:text-success-400 font-medium">✓ Подтверждён — '
                                        . $record->email_verified_at->format('d.m.Y H:i')
                                        . '</span>'
                                    );
                                }

                                return new HtmlString('<span class="text-danger-600 dark:text-danger-400">✗ Не подтверждён</span>');
                            }),

                        Placeholder::make('phone_verified_at')
                            ->label('Статус телефона')
                            ->content(function ($record): HtmlString {
                                if ($record?->phone_verified_at) {
                                    return new HtmlString(
                                        '<span class="text-success-600 dark:text-success-400 font-medium">✓ Подтверждён — '
                                        . $record->phone_verified_at->format('d.m.Y H:i')
                                        . '</span>'
                                    );
                                }

                                return new HtmlString('<span class="text-danger-600 dark:text-danger-400">✗ Не подтверждён</span>');
                            }),
                    ]),

                Section::make('Пароль')
                    ->schema([
                        TextInput::make('password')
                            ->label('Новый пароль')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->minLength(12)
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
