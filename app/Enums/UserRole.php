<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case USER = 'user';
    case PARTNER = 'partner';
    case JUDGE = 'judge';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Администратор',
            self::MODERATOR => 'Модератор',
            self::USER => 'Пользователь',
            self::PARTNER => 'Организатор',
            self::JUDGE => 'Судья',
        };
    }
}
