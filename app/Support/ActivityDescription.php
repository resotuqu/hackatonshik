<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Enums\UserRole;
use Spatie\Activitylog\Models\Activity;

final class ActivityDescription
{
    /**
     * @var array<string, string>
     */
    private const FIELD_LABELS = [
        'title' => 'название',
        'description' => 'описание',
        'is_public' => 'публичность',
        'image_url' => 'обложка',
        'hackaton_id' => 'хакатон',
        'status' => 'статус',
        'start_at' => 'старт',
        'end_at' => 'финиш',
        'registration_deadline_at' => 'дедлайн регистрации',
        'prize_fund' => 'призовой фонд',
        'fio' => 'ФИО',
        'nickname' => 'никнейм',
        'role' => 'роль',
        'suspended_at' => 'блокировка',
    ];

    public static function format(Activity $activity): string
    {
        if ($activity->description === 'status_changed') {
            $status = ApplicationStatus::tryFrom((string) $activity->getProperty('status', ''));

            return $status instanceof ApplicationStatus
                ? 'Статус заявки: '.$status->label()
                : 'Статус заявки изменён';
        }

        if ($activity->description === 'suspension_changed') {
            return filled($activity->getProperty('suspended_at'))
                ? 'Пользователь заблокирован'
                : 'Блокировка снята';
        }

        $changes = $activity->attribute_changes?->toArray() ?? [];
        $attributes = $changes['attributes'] ?? [];
        $old = $changes['old'] ?? [];

        if ($activity->event === 'created') {
            return 'Создано';
        }

        if ($activity->event === 'deleted') {
            return 'Удалено';
        }

        if ($attributes !== []) {
            $parts = [];

            foreach ($attributes as $key => $newValue) {
                $label = self::fieldLabel((string) $key);
                $oldValue = $old[$key] ?? '—';
                $parts[] = sprintf(
                    '%s: %s → %s',
                    $label,
                    self::formatValue($oldValue),
                    self::formatValue($newValue),
                );
            }

            return implode('; ', $parts);
        }

        return $activity->description;
    }

    public static function actorName(Activity $activity): string
    {
        $causer = $activity->causer;

        if ($causer === null) {
            return 'Система';
        }

        return $causer->fio ?? $causer->nickname ?? $causer->email ?? 'Пользователь';
    }

    private static function fieldLabel(string $key): string
    {
        return self::FIELD_LABELS[$key] ?? $key;
    }

    private static function formatValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'да' : 'нет';
        }

        if (is_string($value)) {
            $hackatonStatus = HackatonStatus::tryFrom($value);
            if ($hackatonStatus instanceof HackatonStatus) {
                return $hackatonStatus->label();
            }

            $userRole = UserRole::tryFrom($value);
            if ($userRole instanceof UserRole) {
                return $userRole->label();
            }
        }

        return (string) $value;
    }
}
