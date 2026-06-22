<?php

namespace App\Filament\Resources\PlatformSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlatformSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('key')
                            ->label('Ключ')
                            ->disabled(),

                        TextInput::make('label')
                            ->label('Название')
                            ->disabled(),

                        Textarea::make('description')
                            ->label('Описание')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('value')
                            ->label('Включено')
                            ->formatStateUsing(fn ($state) => (bool) $state)
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0')
                            ->afterStateUpdated(function (): void {
                                // Cache cleared in model observer / page hook
                            }),
                    ]),
            ]);
    }
}
