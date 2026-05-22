<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Hackaton;
use App\Models\User;

class HackatonPolicy
{
    public function view(?User $user, Hackaton $hackaton): bool
    {
        if ($hackaton->is_public) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        if ((int) $hackaton->user_id === (int) $user->id) {
            return true;
        }

        if ($hackaton->isJudge($user)) {
            return true;
        }

        return $hackaton->teams()
            ->where(function ($query) use ($user): void {
                $query
                    ->where('teams.user_id', $user->id)
                    ->orWhereHas('roles', fn ($rolesQuery) => $rolesQuery->where('user_id', $user->id));
            })
            ->exists();
    }

    public function update(User $user, Hackaton $hackaton): bool
    {
        return (int) $hackaton->user_id === (int) $user->id;
    }

    public function delete(User $user, Hackaton $hackaton): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->update($user, $hackaton);
    }

    /**
     * Организатор или назначенный судья (вкладки кейсов / оценки).
     */
    public function viewOrganizationDashboard(User $user, Hackaton $hackaton): bool
    {
        return $this->update($user, $hackaton) || $hackaton->isJudge($user);
    }
}
