<?php

declare(strict_types=1);

namespace App\Enums;

enum OrganizerEntityType: string
{
    case Company = 'company';
    case Individual = 'individual';

    public function label(): string
    {
        return match ($this) {
            self::Company => 'Юридическое лицо',
            self::Individual => 'Физическое лицо',
        };
    }
}
