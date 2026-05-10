<?php

declare(strict_types=1);

namespace App\Enums;

enum HackatonStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case REGISTRATION_OPEN = 'registration_open';
    case REGISTRATION_CLOSED = 'registration_closed';
    case WAITING_START = 'waiting_start';
    case CASES_ANNOUNCED = 'cases_announced';
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
            self::REGISTRATION_CLOSED => 'Регистрация закрыта',
            self::WAITING_START => 'Ожидание старта',
            self::CASES_ANNOUNCED => 'Кейсы объявлены',
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
            self::REGISTRATION_CLOSED => 'badge-warning',
            self::WAITING_START => 'badge-accent',
            self::CASES_ANNOUNCED => 'badge-info',
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
            || $this === self::REGISTRATION_CLOSED
            || $this === self::WAITING_START
            || $this === self::CASES_ANNOUNCED
            || $this === self::IN_PROGRESS
            || $this === self::PUBLISHED;
    }
}
