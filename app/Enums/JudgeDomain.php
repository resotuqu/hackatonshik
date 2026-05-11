<?php

declare(strict_types=1);

namespace App\Enums;

enum JudgeDomain: string
{
    case DESIGN = 'design';
    case DEV = 'dev';
    case BUSINESS = 'business';

    public function label(): string
    {
        return match ($this) {
            self::DESIGN => 'Дизайн',
            self::DEV => 'Разработка',
            self::BUSINESS => 'Бизнес',
        };
    }
}
