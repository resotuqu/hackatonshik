<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['role'])) {
            $role = $data['role'];
            unset($data['role']);

            return tap($data, function () use ($role): void {
                $this->roleForCreate = $role;
            });
        }

        return $data;
    }

    private mixed $roleForCreate = null;

    protected function afterCreate(): void
    {
        if ($this->roleForCreate !== null) {
            $this->record->forceFill(['role' => $this->roleForCreate])->save();
        }
    }
}
