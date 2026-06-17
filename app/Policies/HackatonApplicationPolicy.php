<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\User;

class HackatonApplicationPolicy
{
    public function create(User $user): bool
    {
        return $user->canParticipate();
    }

    public function update(User $user, HackatonApplication $application): bool
    {
        $hackaton = Hackaton::query()->find($application->hackaton_id);

        return $hackaton instanceof Hackaton && $hackaton->user_id === $user->id; // только создатель хакатона
    }

    public function delete(User $user, HackatonApplication $application): bool
    {
        $hackaton = Hackaton::query()->find($application->hackaton_id);
        $team = Team::query()->find($application->team_id);

        return ($hackaton instanceof Hackaton && $hackaton->user_id === $user->id)
            || ($team instanceof Team && $team->user_id === $user->id);
    }
}
