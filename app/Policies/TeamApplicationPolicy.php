<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TeamApplication;
use App\Models\User;

class TeamApplicationPolicy
{
    public function create(User $user): bool
    {
        return $user->canParticipate();
    }

    public function update(User $user, TeamApplication $application): bool
    {
        return (int) $application->teamRole?->team?->user_id === (int) $user->id;
    }

    public function delete(User $user, TeamApplication $application): bool
    {
        return (int) $application->teamRole?->team?->user_id === (int) $user->id
            || (int) $application->user_id === (int) $user->id;
    }
}
