<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;

final class PostLoginRedirect
{
    public static function intendedUrl(User $user): string
    {
        if ($user->isOrganizer()) {
            return route('organizer.dashboard');
        }

        if ($user->isJudge()) {
            return route('judge.dashboard');
        }

        if ($user->isModerator() || $user->isAdmin()) {
            return route('admin.dashboard');
        }

        return route('home');
    }
}
