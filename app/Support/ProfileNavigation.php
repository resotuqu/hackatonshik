<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;

final class ProfileNavigation
{
    public static function hackatonsTabLabel(User $user): string
    {
        if ($user->isOrganizer()) {
            return 'Мои хакатоны';
        }

        if ($user->isJudge()) {
            return 'Назначенные хакатоны';
        }

        return 'Мои заявки и хакатоны';
    }

    public static function hackatonsTabHref(User $user): string
    {
        if ($user->isOrganizer()) {
            return route('organizer.dashboard');
        }

        if ($user->isJudge()) {
            return route('judge.dashboard');
        }

        return route('participant.hackatons');
    }

    public static function isHackatonsTabActive(User $user, ?string $active = null): bool
    {
        return match ($active) {
            'hackatons' => true,
            'personal', 'teams', 'certificates' => false,
            default => request()->routeIs(
                'participant.hackatons',
                'participant.hackatons.hub',
                'organizer.dashboard',
                'organizer.applications',
                'organizer.scoring',
                'organizer.finished',
                'organizer.participants',
                'profile.hackatons',
                'profile.hackatons.applications',
                'profile.hackatons.scoring',
                'profile.hackatons.finished',
                'profile.hackatons.participants',
                'profile.hackatons.hub',
                'judge.dashboard',
                'judge.hackatons.show',
                'judge.cases.submissions',
                'judge.submissions.evaluate',
            ),
        };
    }
}
