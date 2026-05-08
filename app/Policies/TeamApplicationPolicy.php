<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;

class TeamApplicationPolicy
{
    public function create(User $user): bool
    {
        return ! $user->isOrganizer();
    }

    public function update(User $user, TeamApplication $application): bool
    {
        $teamRole = TeamRole::query()->find($application->team_role_id);
        $team = $teamRole instanceof TeamRole ? Team::query()->find($teamRole->team_id) : null;

        return $team instanceof Team && $team->user_id === $user->id; // только создатель команды
    }

    public function delete(User $user, TeamApplication $application): bool
    {
        $teamRole = TeamRole::query()->find($application->team_role_id);
        $team = $teamRole instanceof TeamRole ? Team::query()->find($teamRole->team_id) : null;

        return ($team instanceof Team && $team->user_id === $user->id) || $application->user_id === $user->id;
    }
}
