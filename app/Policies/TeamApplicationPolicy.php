<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TeamApplication;
use App\Models\User;

class TeamApplicationPolicy
{
    public function create(User $user): bool
    {
        return true; // любой авторизованный пользователь может подать заявку
    }

    public function update(User $user, TeamApplication $application): bool
    {
        return $application->teamRole->team->user_id === $user->id; // только создатель команды
    }

    public function delete(User $user, TeamApplication $application): bool
    {
        return $application->teamRole->team->user_id === $user->id || $application->user_id === $user->id;
    }
}
