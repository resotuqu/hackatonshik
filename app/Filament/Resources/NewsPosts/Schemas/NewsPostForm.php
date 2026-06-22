<?php

namespace App\Filament\Resources\NewsPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Публикация')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('category')
                            ->label('Категория')
                            ->default('Обновления')
                            ->maxLength(255),

                        Textarea::make('excerpt')
                            ->label('Краткое описание')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),

                        MarkdownEditor::make('body')
                            ->label('Текст')
                            ->required()
                            ->columnSpanFull(),

                        DateTimePicker::make('published_at')
                            ->label('Дата публикации'),

                        Toggle::make('is_published')
                            ->label('Опубликовано')
                            ->default(false),
                    ]),
            ]);
    }
}
