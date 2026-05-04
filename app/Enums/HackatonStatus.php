<?php

declare(strict_types=1);

namespace App\Enums;

enum HackatonStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case REGISTRATION_OPEN = 'registration_open';
    case IN_PROGRESS = 'in_progress';
    case JUDGING = 'judging';
    case FINISHED = 'finished';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Черновик',
            self::PUBLISHED => 'Опубликован',
            self::REGISTRATION_OPEN => 'Открыта регистрация',
            self::IN_PROGRESS => 'В процессе',
            self::JUDGING => 'Судейство',
            self::FINISHED => 'Завершен',
            self::ARCHIVED => 'Архив',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'badge-ghost',
            self::PUBLISHED => 'badge-info',
            self::REGISTRATION_OPEN => 'badge-success',
            self::IN_PROGRESS => 'badge-primary',
            self::JUDGING => 'badge-warning',
            self::FINISHED => 'badge-neutral',
            self::ARCHIVED => 'badge-ghost',
        };
    }

    public function isFinishedLike(): bool
    {
        return $this === self::FINISHED || $this === self::ARCHIVED;
    }

    public function isActive(): bool
    {
        return $this === self::REGISTRATION_OPEN
            || $this === self::IN_PROGRESS
            || $this === self::PUBLISHED;
    }
}
