<?php

namespace App\Filament\Resources\AvatarPresetPacks\RelationManagers;

use App\Actions\AvatarPreset\DeleteAvatarPresetImage;
use App\Actions\AvatarPreset\StoreAvatarPresetImages;
use App\Models\AvatarPreset;
use App\Support\PresetAvatar;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PresetsRelationManager extends RelationManager
{
    protected static string $relationship = 'presets';

    protected static ?string $title = 'Аватарки';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('storage_path')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('storage_path')
                    ->label('Превью')
                    ->disk('public')
                    ->width(60)
                    ->height(60),

                TextColumn::make('storage_path')
                    ->label('Путь')
                    ->fontFamily('mono')
                    ->limit(60),

                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('upload')
                    ->label('Загрузить аватарки')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form(function (): array {
                        $pack = $this->getOwnerRecord();

                        return [
                            FileUpload::make('files')
                                ->label('Файлы')
                                ->multiple()
                                ->disk('public')
                                ->directory(PresetAvatar::packStorageDirectory($pack->slug))
                                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                                ->maxSize(2048)
                                ->maxFiles(20)
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file) => Str::lower(Str::random(8)).'.'.$file->getClientOriginalExtension()
                                )
                                ->image()
                                ->imagePreviewHeight('80')
                                ->helperText('PNG, JPG, WebP или SVG. До 2 МБ каждый, до 20 за раз.'),
                        ];
                    })
                    ->action(function (array $data): void {
                        $pack = $this->getOwnerRecord();
                        $count = app(StoreAvatarPresetImages::class)($pack, (array) ($data['files'] ?? []));

                        Notification::make()
                            ->title("Загружено: {$count}")
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('moveUp')
                    ->label('')
                    ->icon('heroicon-o-arrow-up')
                    ->tooltip('Выше')
                    ->action(fn (AvatarPreset $record) => $record->update([
                        'sort_order' => max(0, $record->sort_order - 1),
                    ])),

                Action::make('moveDown')
                    ->label('')
                    ->icon('heroicon-o-arrow-down')
                    ->tooltip('Ниже')
                    ->action(fn (AvatarPreset $record) => $record->update([
                        'sort_order' => $record->sort_order + 1,
                    ])),

                Action::make('delete')
                    ->label('Удалить')
                    ->icon('heroicon-o-trash')
                    ->color(Color::Red)
                    ->requiresConfirmation()
                    ->action(fn (AvatarPreset $record) => app(DeleteAvatarPresetImage::class)($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('deleteBulk')
                        ->label('Удалить выбранные')
                        ->icon('heroicon-o-trash')
                        ->color(Color::Red)
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $action = app(DeleteAvatarPresetImage::class);
                            $records->each(fn (AvatarPreset $preset) => $action($preset));
                        }),
                ]),
            ]);
    }
}
