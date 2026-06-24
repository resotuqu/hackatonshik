<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    private bool $emailChanged = false;

    private bool $phoneChanged = false;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revokeEmailVerification')
                ->label('Сбросить верификацию email')
                ->icon('heroicon-o-envelope-open')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Сбросить верификацию email?')
                ->modalDescription('Пользователю придётся заново подтвердить адрес электронной почты.')
                ->modalSubmitActionLabel('Сбросить')
                ->action(function (): void {
                    /** @var User $record */
                    $record = $this->getRecord();
                    $record->forceFill(['email_verified_at' => null])->save();
                    $this->refreshFormData(['email_verified_at']);
                })
                ->visible(fn (): bool => (bool) $this->getRecord()->email_verified_at),

            Action::make('revokePhoneVerification')
                ->label('Сбросить верификацию телефона')
                ->icon('heroicon-o-phone-x-mark')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Сбросить верификацию телефона?')
                ->modalDescription('Пользователю придётся заново подтвердить номер телефона.')
                ->modalSubmitActionLabel('Сбросить')
                ->action(function (): void {
                    /** @var User $record */
                    $record = $this->getRecord();
                    $record->forceFill(['phone_verified_at' => null])->save();
                    $this->refreshFormData(['phone_verified_at']);
                })
                ->visible(fn (): bool => (bool) $this->getRecord()->phone_verified_at),

            Action::make('clearPhone')
                ->label('Очистить телефон')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Очистить номер телефона?')
                ->modalDescription('Номер телефона и статус верификации будут удалены. Пользователю потребуется ввести номер заново.')
                ->modalSubmitActionLabel('Очистить')
                ->action(function (): void {
                    /** @var User $record */
                    $record = $this->getRecord();
                    $record->forceFill(['phone' => null, 'phone_verified_at' => null])->save();
                    $this->refreshFormData(['phone', 'phone_verified_at']);
                })
                ->visible(fn (): bool => filled($this->getRecord()->phone)),

            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var User $record */
        $record = $this->getRecord();

        if (isset($data['role'])) {
            $record->forceFill(['role' => $data['role']])->save();
            unset($data['role']);
        }

        if (isset($data['email']) && $data['email'] !== $record->email) {
            $this->emailChanged = true;
        }

        $incomingPhone = filled($data['phone'] ?? null) ? $data['phone'] : null;
        $existingPhone = filled($record->phone) ? $record->phone : null;
        if ($incomingPhone !== $existingPhone) {
            $this->phoneChanged = true;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var User $record */
        $record = $this->getRecord();

        $updates = [];

        if ($this->emailChanged) {
            $updates['email_verified_at'] = null;
        }

        if ($this->phoneChanged) {
            $updates['phone_verified_at'] = null;
        }

        if ($updates !== []) {
            $record->forceFill($updates)->save();
            $this->refreshFormData(array_keys($updates));
        }
    }
}
