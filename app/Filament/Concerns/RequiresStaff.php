<?php

namespace App\Filament\Concerns;

trait RequiresStaff
{
    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdminOrModerator() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
