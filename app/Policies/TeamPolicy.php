<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function create(User $user): bool
    {
        return $user->canParticipate();
    }

    public function view(?User $user, Team $team): bool
    {
        if ($team->is_public) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        if ((int) $team->user_id === (int) $user->id) {
            return true;
        }

        return $team->roles()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Team $team): bool
    {
        return (int) $team->user_id === (int) $user->id;
    }

    public function chat(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    public function viewActivityHistory(User $user, Team $team): bool
    {
        return (int) $team->user_id === (int) $user->id;
    }
}
