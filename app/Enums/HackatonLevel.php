<?php

declare(strict_types=1);

namespace App\Enums;

enum HackatonLevel: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Pro = 'pro';

    public function label(): string
    {
        return match ($this) {
            self::Beginner => 'Для новичков',
            self::Intermediate => 'Средний',
            self::Advanced => 'Продвинутый',
            self::Pro => 'Профессиональный',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Beginner => 'badge-success',
            self::Intermediate => 'badge-info',
            self::Advanced => 'badge-warning',
            self::Pro => 'badge-error',
        };
    }
}
