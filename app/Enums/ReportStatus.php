<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'На рассмотрении',
            self::Reviewed => 'Рассмотрена',
            self::Dismissed => 'Отклонена',
        };
    }
}
