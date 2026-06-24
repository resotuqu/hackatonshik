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
        $team = $application->teamRole?->team;

        if ($team === null) {
            return false;
        }

        if ((int) $team->user_id === (int) $user->id) {
            return true;
        }

        return $team->roles()
            ->where('user_id', $user->id)
            ->whereHas('role', fn ($query) => $query->where('name', 'капитан'))
            ->exists();
    }

    public function delete(User $user, TeamApplication $application): bool
    {
        return (int) $application->teamRole?->team?->user_id === (int) $user->id
            || (int) $application->user_id === (int) $user->id;
    }
}
